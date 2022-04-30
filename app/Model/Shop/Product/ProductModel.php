<?php

namespace App\Model\Shop\Product;

use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductManufacturer;
use App\Classes\ActiveRecord\Tables\ProductUnit;

class ProductModel
{
    /**
     * Подготавливаем товар к выводу в шаблоне:
     * получем актуальные цены, название единицы измерения, label
     * Возвращает товар или массив товаров готовых к выводу в шаблон
     * @param Product | Product[] $products
     * @return Product[] | Product
     */
    public static function prepareProductsForTemplate(Product|array $products): Product|array
    {
        if ($products) {
            $products = static::prepareProductsUnit($products);
            $products = static::prepareProductsManufacturer($products);
        }

        return $products;
    }


    /**
     * Получаем единицы измерения товара и ярлыки товаров
     * @param Product | Product[] $products
     * @return Product | Product[]
     */
    public static function prepareProductsUnit(Product|array $products): Product|array
    {
        $units = ProductUnit::find()->indexBy()->all();
        if ($products instanceof Product) {
            if (isset($units[$products->unit_id])) {
                $products->unit_id = $units[$products->unit_id]->symbol_national;
            } else {
                $products->unit_id = 'шт.';
            }
        } elseif (!empty($products)) {
            foreach ($products as $key => $product) {
                if (isset($units[$product->unit_id])) {
                    $products[$key]->unit_id = $units[$product->unit_id]->symbol_national;
                } else {
                    $products[$key]->unit_id = 'шт.';
                }
            }
        }
        return $products;
    }

    /**
     * Получаем имя производителя
     * @param Product | Product[] $products
     * @return Product[] | Product
     */
    public static function prepareProductsManufacturer(Product|array $products): Product|array
    {
        if (is_array($products)) {
            $manufacturersID = [];
            foreach ($products as $product) {
                if ($product->manufacturer_id) {
                    $manufacturersID[$product->manufacturer_id] = $product->manufacturer;
                }
            }
            if (!empty($manufacturersID)) {
                $manufacturers = ProductManufacturer::find()->where(['id' => $manufacturersID])->indexBy()->all();
                if ($manufacturers) {
                    foreach ($products as $key => $product) {
                        if ($product->manufacturer_id && isset($manufacturers[$product->manufacturer_id])) {
                            $products[$key]->manufacturer_id = $manufacturers[$product->manufacturer_id]->name;
                        } else {
                            $products[$key]->manufacturer_id = 'С лёгким паром!';
                        }
                    }
                }
            }

        } elseif ($products->manufacturer_id) {
            if ($manufacturer = ProductManufacturer::findOne($products->manufacturer_id)) {
                $products->manufacturer_id = $manufacturer->name;
            } else {
                $products->manufacturer_id = 'С лёгким паром!';
            }
        }
        return $products;
    }

    /**
     * Возвращает товары из того же стиля
     * @param $productID
     * @param int $count нужное количество товаров
     * @return Product|array
     */
    public static function getRelatedProducts($productID, $count = 10): Product|array
    {
        $returnProducts = [];
        if ($baseProduct = Product::findOne($productID)) {
            $styleId = $baseProduct->bath_style_id;
            if ($bathStyle = BathStyle::findOne($styleId)) {
                $relatedProductsId = $bathStyle->products_id;
                if (!empty($relatedProductsId)) {
                    $related10 = array_rand($relatedProductsId, $count);
                    $products = Product::find()->where(['id' => $related10])->all();
                    $returnProducts = static::prepareProductsForTemplate($products);
                }
            }
        }
        return $returnProducts;
    }
}