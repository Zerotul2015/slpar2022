<?php

namespace App\Model\Shop\Product;

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
            $manufacturers = ProductManufacturer::find()->where(['id' => $manufacturersID])->indexBy()->all();
            if ($manufacturers) {
                foreach ($products as $key => $product) {
                    if ($product->manufacturer_id && isset($manufacturers[$product->manufacturer_id])) {
                        $products[$key]->manufacturer_id = $manufacturers[$product->manufacturer_id]->name;
                    }
                }
            }
        } elseif ($products->manufacturer_id) {
            if ($manufacturer = ProductManufacturer::findOne($products->manufacturer_id)) {
                $products->manufacturer_id = $manufacturer->name;
            }
        }
        return $products;
    }
}