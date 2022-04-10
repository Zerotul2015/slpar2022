<?php


namespace App\Model;


use App\Classes\ActiveRecord\Main;
use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\GalleryCategory;
use App\Classes\ActiveRecord\Tables\Menu;
use App\Classes\ActiveRecord\Tables\Page;
use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\ActiveRecord\Tables\Settings;
use App\Classes\ActiveRecord\Tables\SettingsBanner;
use App\Classes\ActiveRecord\Tables\SettingsIndexPage;
use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Classes\ActiveRecord\Tables\SettingsSections;

use App\Classes\Breadcrumbs;
use App\Classes\MyException;


class PublicTemplateModel
{


    public static function templateSettings(string $section, $indexObject)
    {

        $breadcrumb = new Breadcrumbs('Главная', '/');

        $sectionCheck = ['page' => true, 'pageCategory' => true, 'product' => true, 'productCategory' => true, 'cart' => true, 'compare' => true, 'bathStyle' => true];
        if (isset($sectionCheck[$section])) {
            $objectData = match ($section) {
                'page' => Page::findOne($indexObject),
                'pageCategory' => PageCategory::findOne($indexObject),
                'product' => Product::findOne($indexObject),
                'productCategory' => ProductCategory::findOne($indexObject),
                'cart' => $breadcrumb->add('Корзина', '/cart')->render(),
                'compare' => $breadcrumb->add('Сравнение товаров', '/compare')->render(),
                'favorite' => $breadcrumb->add('Избранное', '/favorite')->render(),
                'bathStyle' => BathStyle::findOne($indexObject)
            };
        }
        if (isset($objectData)) {
            $breadcrumb = self::generateBreadcrumb($objectData);
        }

        $footer = PublicTemplateModel::prepareFooter();
        //меню
        $menuCategory = PublicTemplateModel::getMenuCategory();

        return [
            'breadcrumb' => $breadcrumb,
            'bathStyle' => BathStyle::find()->indexBy()->all(),
            'menuCategory' => $menuCategory,
            'footer'=>$footer,
        ];

    }


    /**
     * @param ProductCategory|Product|BathStyle|Page|PageCategory $currentObj
     * @return string
     */
    public static function generateBreadcrumb($currentObj): string
    {
        $breadCrumb = '';
        $nameClass = get_class($currentObj);

        switch ($nameClass) {
            case ProductCategory::class:
                if ($currentObj->parent_id) {
                    if ($categoryParent = ProductCategory::findOne($currentObj->parent_id)) {
                        $breadCrumbClass = new Breadcrumbs('Главная', '/');
                        $breadCrumbClass->add('Каталог', "/");
                        $breadCrumbClass->add($categoryParent->name, "/catalog/$categoryParent->url");
                        $breadCrumbClass->add($currentObj->name, "/catalog/$currentObj->url");
                        $breadCrumb = $breadCrumbClass->render();
                    }
                } else {
                    $breadCrumbClass = new Breadcrumbs('Главная', '/');
                    $breadCrumbClass->add('Каталог', "/");
                    $breadCrumbClass->add($currentObj->name, "/catalog/$currentObj->url");
                    $breadCrumb = $breadCrumbClass->render();
                }
                break;
            case Product::class;
                if ($categoryParent = ProductCategory::findOne($currentObj->category_id)) {
                    $breadCrumbClass = new Breadcrumbs('Главная', '/');
                    if ($categoryParent->parent_id) {
                        if ($categoryParent2 = ProductCategory::findOne($categoryParent->parent_id)) {
                            if ($categoryParent2->parent_id) {
                                if ($categoryParent3 = ProductCategory::findOne($categoryParent2->parent_id)) {
                                    $breadCrumbClass->add($categoryParent3->name, "/catalog/$categoryParent3->url");
                                }
                            }
                            $breadCrumbClass->add($categoryParent2->name, "/catalog/$categoryParent2->url");
                        }
                    }
                    $breadCrumbClass->add($categoryParent->name, "/catalog/$categoryParent->url");
                    $breadCrumbClass->add($currentObj->name, "/product/$currentObj->url");
                    $breadCrumb = $breadCrumbClass->render();
                }
                break;
            case BathStyle::class;
                $breadCrumbClass = new Breadcrumbs('Главная', '/');
                $breadCrumbClass->add('Стилевые решения', "/bath-style");
                $breadCrumbClass->add($currentObj->name, "/bath-style/$currentObj->url");
                $breadCrumb = $breadCrumbClass->render();
                break;
            default:
                break;
        }

        return $breadCrumb;
    }

    /**
     * @param string $type для какой станицы шаблон (допустимые значения page, productCategory, product)
     * @param $primaryKey
     * @return array
     *  title string
     *  description string
     *  menuPage array
     *  menuCategory array
     *  blocks array ['top','sidebar', 'bottom'])
     * @throws MyException
     */
    public static function prepareTemplateValue(string $type = '', $primaryKey = '')
    {
        $settings = Settings::findOne(1);
        $seo = ['title' => '', 'description' => '', 'keywords' => ''];
        $logo = '/images/template/logo_default/' . $settings->image_logo;
        $logoText = $settings->logo_text;

        if (IS_MOBILE) {
            $imageHeaderTemplatePath = 'header_background_mobile';
        } elseif (IS_TABLET) {
            $imageHeaderTemplatePath = 'header_background_tablet';
        } else {
            $imageHeaderTemplatePath = 'header_background';
        }
        $imageFolderPath = '/images/template/';
        $imageHeader = $settings->image_header;
        $slider = [];


        $blocks = ['topBlocks' => false, 'sidebarBlocks' => false, 'bottomBlocks' => false];


        $footer = PublicTemplateModel::prepareFooter();
        //меню
        $menuPage = PublicTemplateModel::prepareMainMenu();
        $menuCategory = PublicTemplateModel::getMenuCategory();

        switch ($type) {
            case 'index':
                $indexPage = SettingsIndexPage::findOne(1);
                $seo = $indexPage->seo;
                if ($indexPage->image_header) {
                    $imageHeader = $indexPage->image_header;
                    $imageFolderPath = '/images/pages/' . $indexPage->folder . '/';
                }
                $blocks = PublicTemplateModel::prepareBlocksForIndexPage($indexPage);
                $slider = PublicTemplateModel::prepareSliderForIndexPage($indexPage);
                $imageHeader = ($indexPage->image_header) ? $indexPage->image_header : $imageHeader;
                break;
            case 'page':
                if (is_null($primaryKey) || empty($primaryKey)) {
                    $blocks = PublicTemplateModel::prepareBlocksLayout(3);
                } elseif ($page = Page::findOne($primaryKey)) {
                    $seo = $page->seo;
                    if ($page->image) {
                        $imageHeader = ($page->image) ? $page->image : $imageHeader;
                        $imageFolderPath = '/images/pages/' . $page->folder . '/';
                    }
                    $blocks = PublicTemplateModel::prepareBlocksLayout(3, $primaryKey);
                } else {
                    throw new MyException('404');
                }
                break;
            case 'gallery':// фотогалерея
                if (empty($primaryKey)) {
                    $blocks = self::prepareBlocksLayout(7);
                } elseif ($galleryCategory = GalleryCategory::findOne($primaryKey)) {
                    $seo = $galleryCategory->seo;
                    if (!$seo['title']) {
                        $seo['title'] = $galleryCategory->category_name;
                    }
//                      TODO узнать нужна ли своя шапка для каждой категории фотогалереи
//                    if ($gallery->image_header) {
//                        $imageHeader = $galleryCategory->image_header;
//                        $imageFolderPath = '/images/gallery/' . $galleryCategory->folder . '/';
//                    }
//                    $imageHeader = ($galleryCategory->image_header) ? $galleryCategory->image_header : $imageHeader;
                    $blocks = self::prepareBlocksLayout(7, $primaryKey);
                } else {
                    throw new MyException('404');
                }
                break;
            case 'giftCertificate':
                $blocks = self::prepareBlocksLayout(6);
                if ($sectionSettings = SettingsSections::findOne(6)) {
                    $seo = $sectionSettings->seo;
                }
                break;
            case'catalog':
                if (is_null($primaryKey) || empty($primaryKey)) {
                    $blocks = PublicTemplateModel::prepareBlocksLayout(1);
                } elseif ($category = ProductCategory::findOne($primaryKey)) {
                    $seo = $category->seo;
                    if (empty($seo['title'])) {
                        if ($category->title) {
                            $seo['title'] = $category->title;
                        } else {
                            $seo['title'] = $category->name;
                        }
                        $seo['title'] = $settings->title_prefix . ' ' . $seo['title'] . ' ' . $settings->title_postfix;
                    }
                    //$imageHeader = ($category->image_design) ? $category->image_design : $imageHeader;
                    $blocks = PublicTemplateModel::prepareBlocksLayout(1, $primaryKey);
                } else {
                    throw new MyException('404');
                }
                break;
            case 'product':
                if ($product = Product::findOne($primaryKey)) {
                    $seo = $product->seo;
                    if (empty($seo['title'])) {
                        $seo['title'] = $product->name;
                        $seo['title'] = $settings->title_prefix . ' ' . $seo['title'] . ' ' . $settings->title_postfix;
                    }
                    //$imageHeaderCategory = ProductCategory::findOne($product->category)->image_design;
                    //$imageHeader = $imageHeaderCategory ? $imageHeaderCategory : $imageHeader;
                    $blocks = PublicTemplateModel::prepareBlocksLayout('product', $primaryKey);
                } else {
                    throw new MyException('404');
                }
                break;
            case 'manufacturer':
                if ($manufacturer = Product::findOne($primaryKey)) {
                    if (empty($seo['title'])) {
                        $seo['title'] = $manufacturer->name;
                    }
                    $blocks = PublicTemplateModel::prepareBlocksLayout('catalog');
                } else {
                    throw new MyException('404');
                }
                break;
            case 'cart':
                $seo['title'] = 'Корзина';
                $blocks = PublicTemplateModel::prepareBlocksLayout('cart');
                break;
            case 'checkout':
                $seo['title'] = 'Оформление заказа';
                $blocks = PublicTemplateModel::prepareBlocksLayout('cart');
                break;
            case 'previewOrder':// страница проверки заказов
                if ($page = Page::find()->where(['url_integrated' => '/order/preview-order/'])->one()) {
                    $seo = $page->seo;
                    $imageHeader = ($page->image) ? $page->image : $imageHeader;
                    $blocks = PublicTemplateModel::prepareBlocksLayout('page', $page->id);
                } else {
                    $seo['title'] = 'Просмотр заказов';
                    $blocks = PublicTemplateModel::prepareBlocksLayout();
                }
                break;
            case 'compare':
                $seo['title'] = 'Сравнение товаров';
                break;
            case '403':
                $seo['title'] = 'Доступ запрещен - 403';
                break;
            case '404':
                $seo['title'] = 'Такой страницы не существует - 404';
                break;
            default:
                //TODO подумать... может стоит так оставить
                $seo['title'] = '';
                $blocks = PublicTemplateModel::prepareBlocksLayout();
                break;
        }
        return [
            'seo' => $seo,
            'logo' => $logo,
            'logo_text' => $logoText,
            'image_header' => $imageFolderPath . $imageHeaderTemplatePath . '/' . $imageHeader,
            'menuPage' => $menuPage,
            'menuCategory' => $menuCategory,
            'slider' => $slider,
            'blocks' => $blocks,
            'footer' => $footer
        ];
    }

    public static function prepareFooter()
    {
        $titleForColumn = ['', '', ''];
        $footerForColumn = [];
        $footerForColumn[] = '<a class="link link_footer link_footer-last-item" href="tel:+79617221122">8(961)722-11-22</a>';
        $footerForColumn[] = '<a class="link link_footer link_footer-last-item" href="tel:+73842480232">8(3842)48-02-32</a>';
        $footerForColumn[] = '<div class="social-block-footer">
<div class="social-link-intro">Следите за нами</div>
<div class="social-link-wrap">
<a class="link link_footer-social link_footer link_footer-last-item" target="blank" href="https://vk.com/kamin42" title="Мы в Вконтакте"><i class="fab fa-vk"></i></a>
</div>
</div>';
        $footerColumns = Settings::findOne(1)->template_footer;
        $arrayPagesID = [];
        foreach ($footerColumns as $columnKey => $column) {
            if (($column['type'] == 'pages') && (isset($column['values']) && !empty($column['values']))) {
                $arrayPagesID = $column['values'] + $arrayPagesID;
            }
        }
        if (!empty($arrayPagesID)) {
            $pages = Page::find()->where([Page::$primaryKey => $arrayPagesID])->indexBy()->all();
        }
        foreach ($footerColumns as $columnKey2 => $column) {
            if (isset($titleForColumn[$columnKey2])) {
                $footerColumns[$columnKey2]['title'] = $titleForColumn[$columnKey2];
            }
            if (isset($footerForColumn[$columnKey2])) {
                $footerColumns[$columnKey2]['footer'] = $footerForColumn[$columnKey2];
            }
            if (isset($pages) && ($column['type'] == 'pages') && (isset($column['values']) && !empty($column['values']))) {
                foreach ($column['values'] as $pageID) {
                    if (isset($pages[$pageID])) {
                        if ($pages[$pageID]->integrated) {
                            $url = $pages[$pageID]->url_integrated;
                        } else {
                            $url = '/page/' . $pages[$pageID]->url;
                        }
                        $footerColumns[$columnKey2]['values'][$pageID] = ['url' => $url, 'title' => $pages[$pageID]->title];
                    } else {
                        unset($footerColumns[$columnKey2]['values'][$pageID]);
                    }
                }
            }
        }
        return $footerColumns;
    }

    /**
     * Возвращает многомерный массив с пунктами меню, дочерние пункты содержатся в элементе с ключем 'children'
     * ссылка содержится в элементе href
     * @return array
     */
    public static function prepareMainMenu()
    {
        //TODO написать новый метод
//        $menu = Menu::find()->sort('ASC')->findAllToArray();
//        $menuLevel1 = [];
//        $menuLevel2 = [];
//        $menuLevel3 = [];
//        //первый проход собираем id всех нужных страниц
//        $pageIDArray = [];
//        $pages = [];
//        foreach ($menu as $menuItem) {
//            $pageIDArray[] = $menuItem['page'] ?? null;
//        }
//        if ($pageIDArray) {
//            $pages = Page::find()->where([Page::$primaryKey => $pageIDArray])->indexBy()->all();
//        }
//
//        foreach ($menu as $keyMenuItem => $menuItem) {
//            //устанавливаем ссылку для страниц
//            if (isset($menuItem['page']) && !empty($menuItem['page'])) {
//                if (isset($pages[$menuItem['page']])) {
//                    if ($pages[$menuItem['page']]->integrated) {
//                        $url = $pages[$menuItem['page']]->url_integrated;
//                    } else {
//                        $url = '/page/' . $pages[$menuItem['page']]->url;
//                    }
//                    $menuItem['href'] = $url;
//                    $menuItem['image'] = $pages[$menuItem['page']]->image;
//                } else {
//                    continue;
//                }
//            }
//
//            if (isset($menuItem['parent_id'])&& $menuItem['parent_id']) {
//                $menuLevel2[$menuItem['id']] = $menuItem;
//            } else {
//                $menuLevel1[$menuItem['id']] = $menuItem;
//            }
//        }
//
//        if ($menuLevel2) {
//            foreach ($menuLevel2 as $menuItem2) {
//                if (isset($menuLevel2[$menuItem2['parent_id']])) {
//                    $menuLevel3[$menuItem2['id']] = $menuItem2;
//                    unset($menuLevel2[$menuItem2['id']]);
//                } else {
//                    $menuLevel1[$menuItem2['parent_id']]['children'][$menuItem2['id']] = $menuItem2;
//                }
//            }
//        }
//        if ($menuLevel3) {
//            foreach ($menuLevel3 as $menuItem3) {
//                // Сокращеный пример $menuLevel1[2]['children'][4]['children'][5] = $menuItem3;
//                $menuLevel1[$menuLevel2[$menuItem3['parent_id']]['parent_id']]['children'][$menuItem3['parent_id']]['children'][$menuItem3['id']] = $menuItem3;
//            }
//        }
//        return $menuLevel1;
    }

    /**
     * @return ProductsCategory[]|NULL
     */
    public static function getMenuCategory()
    {
        //TODO Написать новй метод
//        $categoriesReturn = [];
//        $categories = null;
//        $categoryMain = ProductCategory::find()->where(['parent_id' => 0])->indexBy()->all();
//        $productsID = [];
//        $products = [];
//        if (!empty($categoryMain) && is_array($categoryMain)) {
//            $categoryChild = ProductCategory::find()->where(['parent_id' => array_keys($categoryMain)])->indexBy()->all();
//            foreach ($categoryMain as $tempCategory) {
//                if ($tempCategory->product_for_display) {
//                    $productsID[$tempCategory->product_for_display] = $tempCategory->product_for_display;
//                }
//            }
//            if (!empty($categoryChild) && is_array($categoryChild)) {
//                $categories = $categoryMain + $categoryChild;
//                foreach ($categoryChild as $tempCategory) {
//                    if ($tempCategory->product_for_display) {
//                        $productsID[$tempCategory->product_for_display] = $tempCategory->product_for_display;
//                    }
//                }
//            } else {
//                $categories = $categoryMain;
//            }
//            if (!empty($productsID) && is_array($productsID)) {
//                $products = Product::find()
//                    ->where(['id' => $productsID])
//                    ->select(['id', 'name', 'price', 'price_on_request', 'folder', 'image_main', 'url'])
//                    ->indexBy()
//                    ->all();
//            }
//            foreach ($categories as $categoryID => $category) {
//                if (isset($products[$category->product_for_display])) {
//                    $categories[$categoryID]->product_for_display = $products[$category->product_for_display];
//                }
//                if ($category->parent_id) {
//                    $categoriesReturn[$category->parent_id][] = $category;
//                } else {
//                    $categoriesReturn[0][] = $category;
//                }
//            }
//        }
//        // сортируем по приоритету
//        foreach ($categoriesReturn as $catParentID => $catArrays) {
//            usort($catArrays, function ($a, $b) {
//                return strcmp($b->priority, $a->priority);
//            });
//            $categoriesReturn[$catParentID] = $catArrays;
//        }
//
//        return $categoriesReturn;
    }

    /**
     * @return Services[]|NULL
     */
    public static function getMenuService()
    {
        //с сортровкой по приоритету, по убыванию
        return Services::find()->select(['id', 'service_name', 'short_name', 'url'])->sort()->orderBy('priority')->all();
    }

    public static function prepareBlocksForIndexPage(SettingsIndexPage $indexPage)
    {
        //1: 'Категории товаров',
        //2: 'Подборки товаров',
        //3: 'Страницы',
        //4: 'Услуги',
        //5: 'Наши работы'
        //6: 'Банеры'

        $nameClassForData = [
            1 => 'App\Classes\ActiveRecord\Tables\ProductsCategory',
            2 => 'App\Classes\ActiveRecord\Tables\ProductsCollections',
            3 => 'App\Classes\ActiveRecord\Tables\Page',
            4 => 'App\Classes\ActiveRecord\Tables\Services',
            5 => 'App\Classes\ActiveRecord\Tables\OurWorks',
            6 => 'App\Classes\ActiveRecord\Tables\SettingsBanner',
        ];

        $layouts = $indexPage->layout;
        $needDataGroupType = [];
        $needDataGroupTypeRaw = [];
        foreach ($layouts as $layoutItem) {
            if (isset($needDataGroupTypeRaw[$layoutItem['typeRow']])) {
                $needDataGroupTypeRaw[$layoutItem['typeRow']] = array_merge($needDataGroupTypeRaw[$layoutItem['typeRow']], $layoutItem['blocks']);
            } else {
                $needDataGroupTypeRaw[$layoutItem['typeRow']] = $layoutItem['blocks'];
            }
        }
        foreach ($needDataGroupTypeRaw as $typeRow => $indexNeedData) {
            $needDataGroupType[$typeRow] = $indexNeedData;
        }

        foreach ($nameClassForData as $typeRow => $nameClass) {
            if (isset($needDataGroupType[$typeRow])) {
                if ($typeRow == 2) {// для поборок товаров подготавливаем товары
                    $collections = $nameClass::find()->where(['id' => $needDataGroupType[$typeRow]])->indexBy()->all();
                    if ($collections) {
                        $needDataGroupType[$typeRow] = CollectionsModel::prepareProductCollection($collections);
                    }
                } else {
                    $needDataGroupType[$typeRow] = $nameClass::find()->where(['id' => $needDataGroupType[$typeRow]])->indexBy()->all();
                }
            }

        }


        //присваиваем данные полученные из базы
        foreach ($layouts as $key => $layoutItem) {
            $blocks = [];
            foreach ($layoutItem['blocks'] as $blockId) {
                if (isset($needDataGroupType[$layoutItem['typeRow']][$blockId])) {
                    $blocks[] = $needDataGroupType[$layoutItem['typeRow']][$blockId];
                }
            }
            $layouts[$key]['blocks'] = $blocks;
        }


        return $layouts;
    }

    public static function prepareSliderForIndexPage(SettingsIndexPage $indexPage)
    {
        $slider = $indexPage->slider;
        if (!empty($slider['images'])) {
            if ($banners = SettingsBanner::find()->where(['id' => $slider['images']])->indexBy()->all()) {
                foreach ($slider['images'] as $k => $v) {
                    if (isset($banners[$v])) {
                        $slider['images'][$k] = $banners[$v];
                    }
                }
            }
        }
        return $slider;
    }

    /**
     * Возвращает массив блоков для указаной страницы
     * @param string $typePage для чего шаблон:
     *  1: 'Категории товаров',
     *  2: 'Подборки товаров',
     *  3: 'Страницы',
     *  4: 'Услуги',
     *  5: 'Наши работы',
     *  6: 'Подарочные сертификаты',
     *  7: 'Фотогалерея',
     *  8: 'Стили для бань'
     * @param int $idFor для чего виджеты
     * @return array['topBlocks'=>'','sidebarBlocks'=>'','bottomBlocks'=>'']
     */
    public static function prepareBlocksLayout(string $typePage = '', int $idFor = 0)
    {
        // решить что делать с  методом static::getDataToWidget($widgetsTop['type'], $widgetsTop['value']);

        // стандартные виджеты
        $blocks = ['topBlocks' => [], 'sidebarBlocks' => [], 'bottomBlocks' => []];
        $typePageAvailable = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7];
        if (isset($typePageAvailable[$typePage])) {
            if ($idFor) {
                $layoutSettings = SettingsLayouts::find()->where(['layout_for' => $typePage, 'for_id' => $idFor])->one();
                if (!$layoutSettings) {
                    $layoutSettings = SettingsLayouts::find()->where(['layout_for' => $typePage, 'is_default' => 1])->one();
                }
            } else {
                $layoutSettings = SettingsLayouts::find()->where(['layout_for' => $typePage, 'is_default' => 1])->one();
            }
            if ($layoutSettings) {
                $blocks = PublicTemplateModel::getDataToBlock($layoutSettings);
            }
        }
        return $blocks;
    }

    /**
     * Возвращает блок страниц или подборку товаров готовую для вывода в шаблоне
     * @param SettingsLayouts $layoutSettings
     * @return array
     */
    public static function getDataToBlock(SettingsLayouts $layoutSettings)
    {
        $blocks = ['topBlocks' => [], 'sidebarBlocks' => [], 'bottomBlocks' => []];
        /*
        1: 'Баннер',
        2: 'Подборка товара',
        3: 'Произвольный HTML-блок',
        4: 'Страница'
        */
        $needsGetData = [1 => [], 2 => [], 4 => []];
        $nameClassForBlocks = [1 => 'SettingsBanner', 2 => 'ProductsCollections', 4 => 'Page'];
        $dataForBlocks = [1 => [], 2 => [], 4 => []];
        foreach ($blocks as $positionBlocks => $blocksData) {
            foreach ($layoutSettings->blocks[$positionBlocks] as $blocksRow) {
                foreach ($blocksRow as $block) {
                    if (isset($needsGetData[$block['type']])) {
                        $needsGetData[$block['type']][$block['id']] = $block['id'];
                    }
                }
            }

            //получаем нужные данные для блоков упорядоченных по id
            foreach ($needsGetData as $keyClass => $arrayId) {
                if (isset($nameClassForBlocks[$keyClass])) {
                    /** @var Main $nameClass */
                    $nameClass = '\App\Classes\ActiveRecord\Tables\\' . $nameClassForBlocks[$keyClass];
                    $dataForBlocks[$keyClass] = $nameClass::find()->where(['id' => $arrayId])->indexBy()->all();
                }
            }
            //получаем данные товаров для подборок
            if (!empty($dataForBlocks[2])) {
                $productsArrayId = [];
                foreach ($dataForBlocks[2] as $collectionProducts) {
                    /** @var ProductsCollections $collectionProducts */
                    $productsArrayId = $productsArrayId + $collectionProducts->products;
                }
                $products = [];
                if (!empty($productsArrayId)) {
                    $products = Product::find()->where(['id' => $productsArrayId])->indexBy()->all();
                    $products = ProductsModel::prepareProductsForTemplate($products);
                }
                foreach ($dataForBlocks[2] as $collectionID => $collectionProducts) {
                    foreach ($collectionProducts as $keyProduct => $product) {
                        if (isset($products[$product->id])) {
                            $dataForBlocks[2][$collectionID][$keyProduct] = $products[$product->id];
                        }
                    }
                }
            }


            foreach ($layoutSettings->blocks[$positionBlocks] as $rowIndex => $blocksRow) {
                foreach ($blocksRow as $block) {
                    if ($block['type'] == 3) {
                        $blocks[$positionBlocks][$rowIndex][] = $block['val'];
                    } elseif (isset($dataForBlocks[$block['type']])) {
                        $blocks[$positionBlocks][$rowIndex][] =
                            [
                                'type' => $block['type'],
                                'val' => $dataForBlocks[$block['type']][$block['id']]
                            ];
                    }
                }
            }

        }
        return $blocks;
    }

    /**
     * Генерирует html код для Open Graph
     * @param string $title
     * @param string $type
     * @param string|array $image
     * @param string $description
     * @return string
     */
    public static function generateOpenGraph($title = 'Камин42', $type = 'article', $image = '', $description = '')
    {
        $htmlMeta = '<meta property="og:type" content="' . $type . '" /> 
                     <meta property="og:url" content="' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '"/>
                     <meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES | ENT_HTML5) . '"/>
                     <meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES | ENT_HTML5) . '"/>';

        $htmlMetaImage = '';
        if (is_array($image)) {
            foreach ($image as $key => $imageOne) {
                $htmlMetaImage = $htmlMetaImage . '<meta property="og:image" content="' . $imageOne . '"/>';
            }
        } elseif (!empty($image)) {
            $htmlMetaImage = '<meta property="og:image" content="' . $image . '"/>';
        }

        return $htmlMeta . $htmlMetaImage;
    }
}