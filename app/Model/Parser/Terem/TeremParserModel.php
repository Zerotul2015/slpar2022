<?php

namespace App\Model\Parser\Terem;

use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException;
use Amp\Http\Client\Request;
use Amp\Loop;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\ActiveRecord\Tables\ProductManufacturer;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogCategory;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogManufacturer;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogProduct;
use App\Model\Admin\Images\ImagesModel;
use App\Model\Admin\Product\ProductCategoryModel;
use App\Model\Admin\Product\ProductManufacturerModel;
use App\Model\Admin\Product\ProductModel;
use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use JetBrains\PhpStorm\ArrayShape;
use YooKassa\Model\Supplier;
use function Amp\call;
use function Amp\Promise\all;
use function Amp\Promise\wait;

class TeremParserModel
{

    private static string $siteUrl = 'https://www.teremonline.ru';


    public static function parseCategoriesLinksToPages(): array
    {
        $errors = [];
        Loop::run(static function () {
            $httpClient = HttpClientBuilder::buildDefault();
            $categories = SupplierTeremonlineCatalogCategory::find()->findGroupBy('parent_id');
            $urlsCatSub = [];
            foreach ($categories[0] as $catMain) {
                if (isset($categories[$catMain->id]) && !empty($categories[$catMain->id])) {
                    foreach ($categories[$catMain->id] as $cateSub) {
                        $urlsCatSub[] = 'https://www.teremonline.ru' . $cateSub->link_supplier;
                    }
                }
            }
            $promises = [];
            foreach ($urlsCatSub as $url) {
                $promises[] = $httpClient->request(new \Amp\Http\Client\Request($url));
            }

            $responses = wait(all($promises));

            // обрабатываем первые страницы категорий, собираем массив ссылок на все страницы
            foreach ($responses as $response) {
                $body = yield $response->getBody()->buffer();
                static::parseLinksToPagesCategory($body);
            }

        });
        return $errors;
    }

    //собираем ссылка на все страницы категории и записываем их в базу
    public static function parseLinksToPagesCategory($bodyPage)
    {
        $urls = [];
        $pageCount = 1;
        $document = new Document($bodyPage);
        if ($document->has('#curPage')) {
            $linkRoot = trim($document->first(('#curPage'))->text());
        } else {
            $linkRoot = null;
        }
        if ($document->has('.pagination ul')) {
            $pagination = $document->first(('.pagination ul'));
            $linksPages = $pagination->find('a');
            $countPageLink = count($linksPages);
            if ($countPageLink > 1) {
                $pageCount = $linksPages[$countPageLink - 2]->getAttribute('data-page_num');
            }
        }
        $urls[] = 'https://www.teremonline.ru' . $linkRoot;
        if ($pageCount > 1) {
            while ($pageCount > 1) {
                $urls[] = 'https://www.teremonline.ru' . $linkRoot . '?PAGEN_3=' . $pageCount;
                $pageCount--;
            }
        }
        $category = SupplierTeremonlineCatalogCategory::find()->where(['link_supplier' => $linkRoot])->one();
        $category->links_to_pages = $urls;
        $category->save();
    }


    public static function parseProductsCardsOnCategoryPages($links)
    {
        try {
            \Amp\Loop::run(static function () use ($links) {
                $http = HttpClientBuilder::buildDefault();
                $count = 1;
                foreach ($links as $uri) {
                    if ($count % 200 === 0) {
                        yield delay(1000);
                    }
                    $count++;
                    $promise = call(function () use ($http, $uri) {
                        $response = yield $http->request(new Request($uri));
                        return yield $response->getBody()->buffer();

                    });
                    TeremParserModel::parseProductsCardsOnCategoryPage(yield $promise);
                }
            });

        } catch (HttpException  $e) {
            $errors[] = '<br>Ошибка парсинга карточки товара на странице категории:<br>' . $e;
        }
    }


    /**
     * Принимает HTML код страницы категории.
     * Парсит с нее (по умолчанию так же сохраняет товар в базу)
     * @param string $bodyCategoryPage
     * @param bool $saveAfter
     * @return array
     * @throws InvalidSelectorException
     */
    public static function parseProductsCardsOnCategoryPage(string $bodyCategoryPage, bool $saveAfter = true): array
    {
        $errors = [];
        $document = new Document($bodyCategoryPage);
        if ($document->has('.scfr-items')) {
            foreach ($document->find('.sar-itm') as $productCard) {
                static::parseProductCardOnCategoryPage($productCard, $saveAfter);
            }
        }
        return $errors;
    }

    /**
     * @param Document|Element|Element[] $productCardBody
     * @param bool $saveAfter
     * @return array
     * @throws InvalidSelectorException
     */
    public static function parseProductCardOnCategoryPage(array|Element|Document $productCardBody, bool $saveAfter = true): array
    {
        $product = [];
        $product['link_supplier'] = static::parseProductLink($productCardBody);
        $product['country'] = static::parseProductCountry($productCardBody);
        $product['name'] = static::parseProductName($productCardBody);
        $product['article'] = static::parseProductArticle($productCardBody);
        $product['images'] = static::parseProductImages($productCardBody);
        if ($saveAfter) {
            static::saveParsedProduct($product);
        }
        return $product;
    }


    /**
     * Загружаем и отправляем на парсинг страницу товара
     * @param SupplierTeremonlineCatalogProduct[]|null $products
     * @return array errors если были
     */
    public static function parseProductsPages(array $products): array
    {
        $errors = [];
        $categoriesById = SupplierTeremonlineCatalogCategory::find()->indexBy()->select(['id', 'link_supplier'])->all();
        $manufacturersById = SupplierTeremonlineCatalogManufacturer::find()->indexBy()->select(['id', 'link_supplier'])->all();
        $siteUrl = static::$siteUrl;
        try {
            //$http = HttpClientBuilder::buildDefault();
            $http = (new HttpClientBuilder())
                ->usingPool(new UnlimitedConnectionPool())
                ->build();
            Loop::run(static function () use ($http, $categoriesById, $manufacturersById, $siteUrl, $products) {
                if (!empty($products)) {
                    //$responsePromises = [];
                    foreach ($products as $indexProduct => $product) {
                        //TODO заглушка на первые загрузки

                        $uri = $siteUrl . $product->link_supplier;
                        $promise = call(function () use ($http, $uri) {
                            //return yield $http->request(new Request($uri));
                            $response = yield $http->request(new Request($uri));
                            return yield $response->getBody()->buffer();
                        });
                        TeremParserModel::parseBodyProductPage(yield $promise, $manufacturersById, $categoriesById);
                    }

                    /*if (!empty($responsePromises)) {
                        foreach ($responsePromises as $indexProduct => $response) {
                            $responseAnswer = yield $response;
                            $responseStatus =  $responseAnswer->getStatus();
                            var_dump($responseStatus);
                            if ($responseStatus === '404') {
                                $products[$indexProduct]->removed_from_site = 1;
                                $products[$indexProduct]->save();
                            } elseif ($responseStatus === '200') {
                                TeremParserModel::parseBodyProductPage(yield $response->getBody()->buffer(), $manufacturersById, $categoriesById);
                            }
                        }
                    }*/
                }
            });
        } catch (HttpException  $e) {
            $errors[] = '<br>Ошибка парсинга страницы товара:<br>' . $e;
        }
        return $errors;
    }

    /**
     * Парсим страницу товара.
     * Возвращает массив с данными товара.
     * @param string $bodyPage
     * @param array $manufacturersById
     * @param array $categoriesById
     * @return array
     * @throws InvalidSelectorException
     */
    public static function parseBodyProductPage(string $bodyPage, array $manufacturersById = [], array $categoriesById = [], $saveAfter = true): array
    {
        if (empty($manufacturersById)) {
            $manufacturersById = SupplierTeremonlineCatalogManufacturer::find()->indexBy()->select(['id', 'link_supplier'])->all();
        }
        if (empty($categoriesById)) {
            $categoriesById = SupplierTeremonlineCatalogCategory::find()->indexBy()->select(['id', 'link_supplier'])->all();
        }
        $product = [];
        $document = new Document($bodyPage);
        $product['country'] = static::parseProductCountry($document);
        $product['manufacturer'] = static::parseProductManufacturer($document, $manufacturersById);
        $product['category_id'] = static::parseProductCategory($document, $categoriesById);
        $product['stock_count'] = static::parseProductStock($document);
        $product['stock_status'] = $product['stock_count'] ? 1 : 0;
        // т.к. на странице замороченное название, со страницы категории оно лучше
        //$product['name'] = static::parseProductName($document);
        $product['article'] = static::parseProductArticle($document);
        $product['images'] = static::parseProductImages($document);
        $product['specifications'] = static::parseProductSpecifications($document);
        $priceArray = static::parseProductPrice($document);
        $product['price'] = $priceArray['new'];
        $product['price_old'] = $priceArray['old'];
        if ($saveAfter) {
            static::saveParsedProduct($product);
        }
        return $product;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @param array $categoriesById
     * @return string|null ID Категории товара поставщика в нашей базе
     * @throws InvalidSelectorException
     */
    public static function parseProductCategory(array|Element|Document $document, array $categoriesById = []): string|null
    {
        $categoryID = null;
        if ($document->has('#curPage')) {//страница товара
            $uriRelative = trim($document->find('#curPage')[0]->text());
            $urlCategory = preg_replace('/[\w|-]+\/$/', '', $uriRelative);
            if ($categoriesById || $categoriesById = SupplierTeremonlineCatalogCategory::find()->indexBy()->select(['id', 'link_supplier'])->all()) {
                foreach ($categoriesById as $category) {
                    if ($category->link_supplier === $urlCategory) {
                        $categoryID = $category->id;
                        break;
                    }
                }
            }
        }
        return $categoryID;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @param array $manufacturersById
     * @return string|null ID Производителя товара поставщика в нашей базе поставщика
     * @throws InvalidSelectorException
     */
    public static function parseProductManufacturer(array|Element|Document $document, array $manufacturersById = []): string|null
    {
        $manufacturerID = null;
        if ($document->has('.brand_element_block')) {//страница товара
            $manufacturerBlock = $document->first('.brand_element_block');
            if ($manufacturerBlock->has('a')) {
                $linkManufacturerRelative = $manufacturerBlock->first('a');
                $linkManufacturer = static::$siteUrl . $linkManufacturerRelative->getAttribute('href');
            }
            if (isset($linkManufacturer) && (!empty($manufacturers) || $manufacturers = SupplierTeremonlineCatalogManufacturer::find()->select(['id', 'link_supplier'])->all())) {
                foreach ($manufacturers as $manufacturer) {
                    if ($manufacturer->link_supplier === $linkManufacturer) {
                        $manufacturerID = $manufacturer->id;
                        break;
                    }
                }
            }
        }
        return $manufacturerID;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return string|null Количество товара в наличии
     * @throws InvalidSelectorException
     */
    public static function parseProductStock(array|Element|Document $document): string|null
    {
        $stockCount = null;
        if ($document->has('.s-catalog-detail div')) {//страница товара
            $sCatalogDefaultDiv = $document->find('.s-catalog-detail div');
            $sCatalogDefaultDiv = $sCatalogDefaultDiv[1] ?? null;
            if ($sCatalogDefaultDiv->has('meta')) {
                $meta = $sCatalogDefaultDiv->find('meta');
                foreach ($meta as $metaItem) {
                    if ($metaItem->getAttribute('itemprop') === 'offerCount') {
                        $stockCount = $metaItem->getAttribute('content');
                    }
                }
            }
        }
        return $stockCount;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return array['new'=>..., 'old'=>...] Цена товара
     * @throws InvalidSelectorException
     */
    #[ArrayShape(['new' => "float|null", 'old' => "float"])]
    public static function parseProductPrice(array|Element|Document $document): array
    {
        $priceNewOld = ['new' => null, 'old' => null];
        //price
        if ($document->has('.newPriceDetail-js')) {
            $priceRAW = $document->find('.newPriceDetail-js')[0]->text();
            $price = str_replace(' ', '', $priceRAW);
            $priceNewOld['new'] = floatval($price);
        }
        //price old(if discount)
        if ($document->has('.oldPriceDetail-js')) {
            $priceOldRAW = $document->find('.oldPriceDetail-js')[0]->text();
            $priceOld = str_replace(' ', '', $priceOldRAW);
            $priceNewOld['old'] = floatval($priceOld);
        }

        return $priceNewOld;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return array Характеристики товара
     * @throws InvalidSelectorException
     */
    public static function parseProductSpecifications(array|Element|Document $document): array
    {
        $specifications = [];
        if ($document->has('#character')) {
            $tabsChar = $document->first('#character');
            if ($tabsChar->has('.sced-l-itm')) {
                $charLines = $tabsChar->find('.sced-l-itm');
                foreach ($charLines as $charLineItem) {
                    if ($charLineItem->has('.sced-l-descr-1') && $charLineItem->has('.sced-l-descr-2')) {
                        $specName = $charLineItem->first('.sced-l-descr-1')->text();
                        if ($specName !== 'Категория' && $specName !== 'Подкатегория') {
                            $specVal = $charLineItem->first('.sced-l-descr-2')->text();
                            $specifications[] = [
                                'name' => $specName,
                                'val' => $specVal
                            ];
                        }
                    }
                }
            }
        }
        return $specifications;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return array Изображения товара
     * @throws InvalidSelectorException
     */
    public static function parseProductImages(array|Element|Document $document): array
    {
        $images = [];
        if ($document->has('.svs-img')) {//страница товара
            $imagesRaw = $document->find('.svs-img');
            foreach ($imagesRaw as $imageRaw) {
                $linkImage = $imageRaw->first('img')->getAttribute('big_foto');
                if ($linkImage !== '/img/photo-empty.png' && !empty($linkImage)) {
                    $images[] = static::filterImageUriWatermark(static::$siteUrl . $linkImage);
                }
            }
        } elseif ($document->has('.sar-itm .sar-img img')) {//карточка товара на странице категории
            $imageRaw = static::$siteUrl . $document->first('.sar-itm .sar-img img')->getAttribute('src');
            if ($imageRaw !== '/img/photo-empty.png' && !empty($imageRaw)) {
                $images[0] = static::filterImageUriWatermark($imageRaw);
            }
        }
        return $images;
    }

    /**
     * Возвращает ссылки без водяного знака из обычной ссылки.
     * Принимает ссылку или массив ссылок на изображение товара с сайта.
     * Возвращает ссылку на изображение без водяного знака.
     * @param array|string $uri Ссылка или массив ссылок на изображение товара с сайта.
     * @return array|string|null Ссылка или массив на изображение без водяного знака.
     */
    public static function filterImageUriWatermark(array|string $uri): array|string|null
    {
        //https://www.teremonline.ru/upload/resize_cache/iblock/7b7/1024_1024_1f0ccde5e7a13ae51894a3eef4fcac3e6/rg008o0pqjcp6c.jpg
        $uriWithoutWatermark = preg_replace('/(resize_cache\/)(iblock\/\w+\/)(\w+\/)/', '$2', $uri);
        //https://www.teremonline.ru/upload/iblock/7b7/rg008o0pqjcp6c.jpg
        return $uriWithoutWatermark;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return string|null Артикул товара
     * @throws InvalidSelectorException
     */
    public static function parseProductArticle(array|Element|Document $document): string|null
    {
        $article = null;
        if ($document->has('.sc-te-short-descr-1')) {//страница товара
            $articleRAW = $document->first('.sc-te-short-descr-1')->text();
            $article = trim(str_replace('Арт: ', '', $articleRAW));
        } elseif ($document->has('.sar-artic')) { //карточка товара на странице категории
            $articleRAW = $document->first('.sar-artic')->text();
            $article = trim(str_replace('Арт: ', '', $articleRAW));
        }
        return $article;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return string|null Название товара
     * @throws InvalidSelectorException
     */
    public static function parseProductName(array|Element|Document $document): string|null
    {
        $name = null;
        if ($document->has('.sc-te-hdr')) {//страница товара
            ///////////
            ///
            if ($document->has('.wrapper__content .s-catalog-detail')) {
                $wrapWithMeta = $document->find('.wrapper__content .s-catalog-detail')[0]->find('div')[0];
                if ($wrapWithMeta->has('meta')) {
                    $metaTags = $wrapWithMeta->find('meta');
                    $brandString = '';
                    $nameString = '';
                    $mpnString = '';
                    $descriptionString = '';
                    $descReplace = ['Купить ', ' в Москве и Санкт-Петербурге по выгодной цене с доставкой в интернет-магазине Терем.'];
                    foreach ($metaTags as $meta) {
                        if ($meta->getAttribute('itemprop') === 'brand' && empty($brandString)) {
                            $brandString = $meta->getAttribute('content');
                        }
                        if ($meta->getAttribute('itemprop') === 'name' && empty($nameString)) {
                            $nameString = $meta->getAttribute('content');
                        }
                        if ($meta->getAttribute('itemprop') === 'mpn' && empty($mpnString)) {
                            $mpnString = $meta->getAttribute('content');
                        }
                        if ($meta->getAttribute('itemprop') === 'description' && empty($mpnString)) {
                            $descriptionString = $meta->getAttribute('content');
                        }
                    }
                    //$name = preg_replace("/^.*$brandString\s+/", '', $nameString);
                    $name = str_replace($descReplace, '', $descriptionString);
                }
            }
            //////////
            //$name = trim($document->first('.sc-te-hdr')->text());
            $name = trim($name);
        } elseif ($document->has('.sar-p')) { //карточка товара на странице категории
            $name = $document->first('.sar-p')->getAttribute('content');
        }
        return $name;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return string|null Страна производитель товара
     * @throws InvalidSelectorException
     */
    public static function parseProductCountry(array|Element|Document $document): string|null
    {
        $country = null;
        if ($document->has('.ted-c-descr')) {//страница товара
            $country = $document->first('.ted-c-descr')->text();
        } elseif ($document->has('.sar-country')) { //карточка товара на странице категории
            $country = $document->first('.sar-country')->text();
        }
        return $country;
    }

    /**
     * @param Document|Element|Element[] $document Объект Document созданный из страницы товара с сайта teremonline.ru
     * @return string|null Страна производитель товара
     * @throws InvalidSelectorException
     */
    public static function parseProductLink(array|Element|Document $document): string|null
    {
        $link = null;

        if ($document->has('.s-catalog-detail') && $document->has('#curPage')) { //страница товара
            $link = trim($document->first('#curPage')->text());
        } elseif ($document->has('a.sar-content-description')) { //карточка товара на странице категории
            $link = $document->first('a.sar-content-description')->getAttribute('href');
        }
        return $link;
    }

    /**
     * Сохраняем товар в базу партнерских товар
     * @param array $product
     * @param null $id
     * @return array
     */
    #[ArrayShape(['id' => "mixed|null", 'result' => "bool"])]
    public static function saveParsedProduct(array $product, $id = null)
    {
        $result = ['id' => null, 'result' => false];
        if ($id && $productInBase = SupplierTeremonlineCatalogProduct::findOne($id)) {
            $productInBase->set($product);
        } else {
            if (isset($product['article']) && !empty($product['article'])) {
                $productInBase = SupplierTeremonlineCatalogProduct::find()
                    ->where(['article' => $product['article']])
                    ->one();
                if ($productInBase) {
                    $productInBase->set($product);
                } else {
                    $productInBase = SupplierTeremonlineCatalogProduct::create($product);
                }

            } else {
                $productInBase = SupplierTeremonlineCatalogProduct::create($product);
            }
        }
        if ($productInBase->save()) {
            $result = ['id' => $productInBase->id, 'result' => true];
        }
        return $result;
    }

    public static function fileGetContentsCurl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function downloadImagesProducts($products)
    {
        $errors = [];
        try {
            //$http = HttpClientBuilder::buildDefault();
            $http = (new HttpClientBuilder())
                ->usingPool(new UnlimitedConnectionPool())
                ->build();
            Loop::run(static function () use ($http, $products) {
                if (!empty($products)) {
                    foreach ($products as $product) {
                        if (!empty($product->images)) {
                            $newFileSystem = new \App\Model\FilesystemModel();
                            $images = $product->images;
                            foreach ($images as $indexImage => $imageUrl) {
                                $imageUpload = static::fileGetContentsCurl($imageUrl);
                                if ($imageUpload) {
                                    $newName = $indexImage . '.jpg';
                                    $result = file_put_contents(\App\Model\FilesystemModel::DIR_TEMP . DIRECTORY_SEPARATOR . $newName, $imageUpload);
                                    if ($result) {
                                        $returnRelativePath = '/' . $newFileSystem->makePathRelative(\App\Model\FilesystemModel::DIR_TEMP, \App\Model\FilesystemModel::ROOT_SITE) . $newName;
                                        //если это изображение оптимизруем его(lossless)
                                        if ($newFileSystem->CheckTypeFile(\App\Model\FilesystemModel::DIR_TEMP . DIRECTORY_SEPARATOR . $newName) === 'image') {
                                            ImagesModel::optimizeImage(\App\Model\FilesystemModel::DIR_TEMP . DIRECTORY_SEPARATOR . $newName);
                                        }
                                        $newFileSystem->moveFileFromTemp($newName, '/supplier/terem/images/products/' . $product->id . DIRECTORY_SEPARATOR);
                                    }
                                }
                            }
                        }
                    }
                }
            });
        } catch (HttpException  $e) {
            $errors[] = '<br>Ошибка парсинга страницы товара:<br>' . $e;
        }
        return $errors;
    }


    /**
     * Загружает и передает на парсинг страницы производителей
     *
     * @return array
     */
    public static function parseBrands(): array
    {
        $errors = [];

        Loop::run(static function () {
            $brandsUrls = [];
            $document = new Document(file_get_contents(static::$siteUrl . '/brands/?brand_word=all'));
            $brandsWrap = $document->first('.s-brand-catalog .sbc-items');
            foreach ($brandsWrap->find('.sbc-itm') as $brandElementItem) {
                $brandsUrls[] = static::$siteUrl . $brandElementItem->first('.sbc-top')->getAttribute('href');
            }
            $countUrls = count($brandsUrls);
            echo "Всего брендов: ", $countUrls;


            $httpClient = HttpClientBuilder::buildDefault();;
            $requestHandler = static function ($uri) use ($httpClient): \Generator {
                $response = yield $httpClient->request(new Request($uri));
                return yield $response->getBody()->buffer();
            };

            try {
                $promises = [];
                foreach ($brandsUrls as $keyUrl => $brandUrl) {
                    $promises[$keyUrl] = call($requestHandler, $brandUrl);
                    if ($countUrls) {
                        yield \Amp\delay(100);
                    }
                }

                $pagesBrandsBodies = yield $promises;

                foreach ($pagesBrandsBodies as $keyBody => $body) {
                    static::parseBrandPage((string)$body, $brandsUrls[$keyBody]);
                }

            } catch (HttpException $error) {
                $errors[] = $error;
            }
        });
        return $errors;
    }


    /**
     * Парсим страницу бренда и сохраняем его в базу.
     * @return array Массив с ошибками, если были.
     * @throws InvalidSelectorException
     */
    public static function parseBrandPage(string $brandPage, $brandUrl = null): array
    {
        $error = [];
        $documentBrand = new Document($brandPage);
        $brand = [
            'name' => trim($documentBrand->first('h1')->text()),
            'link_image' => $documentBrand->has('.sbdt-r-logo img') ? $documentBrand->first('.sbdt-r-logo img')->getAttribute('src') : null,
            'description' => $documentBrand->has('.s-brand-detail .sbd-middle .sbdm-left') ? $documentBrand->first('.s-brand-detail .sbd-middle .sbdm-left')->innerHtml() : null,
            'link_supplier' => $brandUrl,
        ];
        $brand['link_image'] = static::$siteUrl . $brand['link_image'];
        $brandInBase = SupplierTeremonlineCatalogManufacturer::find()->where(['name' => $brand['name']])->one();
        if (!$brandInBase) {
            if (!SupplierTeremonlineCatalogManufacturer::create($brand)->save()) {
                $error[] = "Не удалось сохранить производителя <a href='$brand[link_supplier]'>$brand[name]</a>";
            }
        }
        return $error;
    }

    /**
     * Возвращает строку со списком ошибок если они были, иначе пустую строку
     * @return array
     * @throws InvalidSelectorException
     */
    public static function parseCategories(): array
    {
        $errors = [];
        $page = file_get_contents(static::$siteUrl . '/catalog/');
        $document = new Document($page);
        if ($document->has('.sc-itm.itemSection-js')) {
            $subCatUrls = [];
            //блоки с основной категорией и ее подкатегориями
            foreach ($document->find('.sc-itm.itemSection-js') as $blockMainCats) {
                //ссылка с названием главной категории .sc-i-top
                $categoryMain = [
                    'name' => $blockMainCats->first('.sc-i-top .sc-i-hdr')->text(),
                    'parent_id' => 0,
                    'link_supplier' => $blockMainCats->first('.sc-i-top')->getAttribute('href')
                ];
                $parentId = static::saveCatInBase($categoryMain);
                if ($parentId === false) {
                    $errors[] = 'Не удалось записать в базу одну из главных категорий (' . $blockMainCats->first('.sc-i-top')->innerHtml() . '), все ее подкатегории так же пропущены.';
                } else {
                    //подкатегории основных разделов
                    if ($blockMainCats->has('.sub_category_list .sc-i-text__container a')) {
                        $subCatHtml = $blockMainCats->find('.sub_category_list .sc-i-text__container a');
                        unset($subCatHtml[0]);
                        foreach ($subCatHtml as $subCatLink) {
                            $categorySub = [
                                'name' => $subCatLink->text(),
                                'parent_id' => $parentId,
                                'link_supplier' => $subCatLink->getAttribute('href')
                            ];
                            $catSubId = static::saveCatInBase($categorySub);
                            if ($catSubId === false) {
                                $errors[] = 'Не удалось записать в базу  Подкатегорию (' . $subCatLink->html() . ') её родитель (' . $blockMainCats->first('.sc-i-top')->html() . '). Все ее подкатегории так же пропущены.';
                            } else {
                                $subCatUrls[$catSubId][] = static::$siteUrl . $categorySub['link_supplier'];
                            }
                        }

                    }
                }
            }

            if (!empty($subCatUrls)) {
                Loop::run(static function () use ($subCatUrls) {
                    $httpClient = HttpClientBuilder::buildDefault();
                    $requestHandler = static function (string $uri) use ($httpClient): \Generator {
                        $response = yield $httpClient->request(new Request($uri));
                        return yield $response->getBody()->buffer();
                    };

                    $promisesSubCat = [];
                    foreach ($subCatUrls as $catParentId => $catUrls) {
                        foreach ($catUrls as $catUrl) {
                            $promisesSubCat[$catParentId][] = call($requestHandler, $catUrl);
                        }
                    }

                    $catBodies = $promisesSubCat;
                    foreach ($catBodies as $parentId => $catBodyArray) {
                        $bodyArray = yield $catBodyArray;
                        foreach ($bodyArray as $body) {
                            static::parseSubCatPage($body, $parentId);
                        }
                    }
                });
            }
        }

        return $errors;
    }

    /**
     * Парсит подкатегорию.
     * @throws InvalidSelectorException
     */
    public static function parseSubCatPage(string $bodyPage, $parentId): array
    {
        $errors = [];
        $document = new Document($bodyPage);
        if ($document->has('.scfr-new-items .scfr-ni')) {
            foreach ($document->find('.scfr-new-items .scfr-ni') as $subCatLink) {
                $category = [
                    'name' => $subCatLink->first('.scfr-nd-1')->text(),
                    'parent_id' => $parentId,
                    'link_supplier' => $subCatLink->getAttribute('href')
                ];
                if (static::saveCatInBase($category) === false) {
                    $errors[] = '<br>Не удалось записать в базу подкатегорию (' . $category['name'] . ')';
                }
            }
        }
        return $errors;
    }

    /**
     * Сохраняет категорию в базу.
     * Возвращает id категории в базе или false
     * @return int | false
     */
    public static function saveCatInBase($catData): bool|int
    {

        $existCategory = SupplierTeremonlineCatalogCategory::find()
            ->andWhere([
                'name' => $catData['name'], 'parent_id' => $catData['parent_id']
            ])
            ->andWhere([
                'link_supplier' => $catData['link_supplier']
            ],
                null, '=', '1', 'OR')
            ->one();
        if ($existCategory) {
            $existCategory->set($catData);
            $existCategory->save();
        } else {
            $existCategory = SupplierTeremonlineCatalogCategory::create($catData);
        }
        $existCategory->save();

        return $existCategory->id;
    }


    /**
     * TODO В будущем переписать на поиск соответствий по наименованию с учетом морфологии.
     * TODO адаптировать на повторное использование.
     * !!!Подходит только для одноразовой первичной загрузки категорий терема в наши!!!
     * @param SupplierTeremonlineCatalogCategory[] $categoriesById
     * @return array error
     */
    #[ArrayShape(['errors' => "bool", 'errorsList' => "array"])] public static function matchingCategories(array $categoriesById): array
    {
        $results = ['errors' => false, 'errorsList' => []];
        if (!empty($categoriesById)) {
            //сначала собираем parent_id что бы за один запрос получить их из базы
            $parentsIdArray = [];
            foreach ($categoriesById as $category) {
                if ($category->parent_id) {
                    $parentsIdArray[] = $category->parent_id;
                }
            }
            if (!empty($parentsIdArray)) {
                $categoriesParentById = SupplierTeremonlineCatalogCategory::find()->where('id', $parentsIdArray)->indexBy()->all();
            }
            //теперь сопоставляем категории поставщика с нашими
            $categoriesParentByIdOur = [];
            foreach ($categoriesById as $category) {
                if (!$category->associated_category_id) {
                    $categoryOur['name'] = $category->name;
                    $categoryOur['associated_suppliers']['terem'] = $category->id;

                    //если у категории есть родитель
                    if ($category->parentId && isset($categoriesParentById[$category->parentId])) {
                        //Проверяем что род. категория сопоставлена с нашей и связанная категория уже получена из базы,
                        //в противном случае получаем ее из базы
                        if ($categoriesParentById[$category->parentId]->associated_category_id) {
                            if (!isset($categoriesParentByIdOur[$categoriesParentById[$category->parentId]->associated_category_id])) {
                                $categoriesParentByIdOurTemp = ProductCategory::findOne($categoriesParentById[$category->parentId]->associated_category_id);
                                if ($categoriesParentByIdOurTemp) {
                                    $categoriesParentByIdOur[$categoriesParentByIdOurTemp->id] = $categoriesParentByIdOurTemp;
                                }
                            }
                        }

                        // если в базе есть родительская категория тогда записываем их связь
                        if (isset($categoriesParentByIdOur[$categoriesParentById[$category->parentId]->associated_category_id])) {
                            $categoryOur['parent_id'] = $categoriesParentByIdOur[$categoriesParentById[$category->parentId]->associated_category_id]->id;
                        }
                    }
                    //если создаем новую категорию тогда приводим значение к нужной структуре.
                    if(!isset($categoryOur['seo'])){
                        $categoryOur['seo'] =['title'=>'','description'=>''];
                    }
                    $resultSave = ProductCategoryModel::Save($categoryOur);
                    if ($resultSave['result'] === true) {
                        $category->associated_category_id = $resultSave['id'];
                        $category->save();

                    } else {
                        $results['errors'] = true;
                        $results['errorsList'][] = "Ошибка сохранения категории с данными: " . json_encode($categoryOur);
                    }
                }
            }
        }
        return $results;
    }


    /**
     * TODO В будущем переписать на поиск соответствий по наименованию с учетом морфологии.
     * TODO адаптировать на повторное использование.
     * !!!Подходит только для одноразовой первичной загрузки товаров терема в наши!!!
     * @param SupplierTeremonlineCatalogProduct[] $products
     * @return array
     */
    #[ArrayShape(['errors' => "bool", 'errorsList' => "array"])] public static function matchingProducts(array $products): array
    {
        $results = ['errors' => false, 'errorsList' => []];
        if (!empty($products)) {
            $categories = SupplierTeremonlineCatalogCategory::find()->indexBy()->all();
            $brands = SupplierTeremonlineCatalogManufacturer::find()->indexBy()->all();
            foreach ($products as $product) {
                $categoryId =
                    (isset($categories[$product->category_id]) && $categories[$product->category_id]->associated_category_id)
                        ? $categories[$product->category_id]->associated_category_id : null;
                $brandId =
                    (isset($brands[$product->manufacturer]) && $brands[$product->manufacturer]->associated_manufacturer_id)
                        ? $brands[$product->manufacturer]->associated_manufacturer_id : null;
                $productOur = [
                    'name' => $product->name,
                    'article' => $product->article,
                    'category_id' => $categoryId,
                    'manufacturer_id' => $brandId,
                    'price' => $product->price,
                    'price_old' => $product->price_old,
                    'country' => $product->country,
                    'characteristics' => $product->specifications,
                ];

                //TODO в будущем сделать более умную проверку названия на похожесть
                //проверяем нет ли у нас товара с таким артикулом
                $productOurFind = Product::find();
                if (!empty($product->article)) {
                    $productOurFind->andWhere(['article' => $product->article], null, '=', 0, 'OR');
                }
                if (!empty($product->name)) {
                    $productOurFind->andWhere(['name' => $product->name], null, '=', 0, 'OR');
                }
                if ($productOurFind->one()) {
                    $productOur = $productOurFind->set($productOur)->toArray();
                }
                //если создаем новый товар тогда приводим значение к нужной структуре.
                if(!isset($productOur['seo'])){
                    $productOur['seo'] =['title'=>'','description'=>''];
                }
                //-----------

                $productOur['associated_suppliers']['terem'] = $product->id;
                $resultSave = ProductModel::Save($productOur);
                if ($resultSave['result'] === true) {
                    $product->associated_product_id = $resultSave['id'];
                    $product->save();

                } else {
                    $results['errors'] = true;
                    $results['errorsList'][] = "Ошибка сохранения категории с данными: " . json_encode($productOur);
                }
            }
            return $results;
        }
        return $results;
    }


    /**
     * TODO В будущем переписать на поиск соответствий по наименованию с учетом морфологии.
     * TODO адаптировать на повторное использование.
     * !!!Подходит только для одноразовой первичной загрузки производителей терема в наши!!!
     * @param SupplierTeremonlineCatalogManufacturer[] $brands
     * @return array
     */
    #[ArrayShape(['errors' => "bool", 'errorsList' => "array"])] public static function matchingBrands(array $brands): array
    {
        $results = ['errors' => false, 'errorsList' => []];
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                if (!$brand->associated_manufacturer_id || !ProductManufacturer::findOne($brand->associated_manufacturer_id)) {
                    $brandOur = [
                        'name' => $brand->name,
                    ];
                    $brandOur['associated_suppliers']['terem'] = $brand->id;
                    $resultSave = ProductManufacturerModel::Save($brandOur);
                    if ($resultSave['result'] === true) {
                        $brand->associated_manufacturer_id = $resultSave['id'];
                        $brand->save();
                    } else {
                        $results['errors'] = true;
                        $results['errorsList'][] = "Ошибка сохранения производителя с данными: " . json_encode($brandOur);
                    }
                }

            }
            return $results;
        }
        return $results;
    }

}