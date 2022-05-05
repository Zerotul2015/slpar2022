<?php

namespace App\Model\Admin\Export;

use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use Bukashk0zzz\YmlGenerator\Cdata;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;

class ExportModel
{
    public static function generateYml($categoriesId = null, $productsId = null)
    {
        $file = tempnam(sys_get_temp_dir(), 'YMLGenerator');
        $settings = (new Settings())
            ->setOutputFile($file)
            ->setEncoding('UTF-8');


        // Creating ShopInfo object (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#shop)
        $shopInfo = (new ShopInfo())
            ->setName('С лёгким паром!')
            ->setCompany('ООО "Триумф"')
            ->setUrl('https://slpar.ru/');

        // Creating currencies array (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#currencies)
        $currencies = [];
        $currencies[] = (new Currency())
            ->setId('RUB')
            ->setRate(1);

        // Creating categories array (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#categories)
        $categoriesCatalog = ProductCategory::findAll();
        $categories = [];
        foreach ($categoriesCatalog as $cat) {
            if ($cat->parent_id) {
                $categories[] = (new Category())
                    ->setId($cat->id)
                    ->setName($cat->name)
                    ->setParentId($cat->parent_id);
            } else {
                $categories[] = (new Category())
                    ->setId($cat->id)
                    ->setName($cat->name);
            }
        }

        // Creating offers array (https://yandex.ru/support/webmaster/goods-prices/technical-requirements.xml#offers)
        $productsCatalog = Product::findAll();
        $offers = [];
        foreach ($productsCatalog as $product) {
            $images = $product->images;
            $callbackFn = fn(string $image):string => 'https://slpar.ru/product/' . $product->folder . '/1280/' . $image;
            $imagesUrl = array_map($callbackFn, $images);

            $product->description = $product->description?:'';
            $offers[] = (new OfferSimple())
                ->setId($product->id)
                ->setAvailable(true)
                ->setUrl('https://slpar.ru/product/' . $product->url)
                ->setPrice($product->price)
                ->setOldPrice($product->price_old)
                ->setPictures($imagesUrl)
                ->setCurrencyId('RUB')
                ->setCountryOfOrigin('Россия')
                ->setCategoryId($product->category_id)
                ->setDelivery(false)
                ->setDescription(new Cdata($product->description))
                ->setName($product->name);
        }

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $categories,
            $offers,
        );


        return $file;
    }
}