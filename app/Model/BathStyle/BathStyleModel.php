<?php

namespace App\Model\BathStyle;

use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use JetBrains\PhpStorm\ArrayShape;

class BathStyleModel
{

    #[ArrayShape(['products' => "\App\Classes\ActiveRecord\Tables\Product[]", 'productsCategory' => "\App\Classes\ActiveRecord\Tables\ProductCategory[]"])]
    public static function getProductsData(int $bathStyleId): array
    {
        $products = static::getProducts($bathStyleId);
        $productsCategory = static::getProductsCategory(null, $products);
        return [
            'products' => $products,
            'productsCategory' => $productsCategory
        ];
    }

    /**
     * Возвращает массив товаров индексированных по id и сгрупированных по категориям для указаного стиля
     * @param BathStyle|int $bathStyleOrKey
     * @return Product[]||[]
     */
    public static function getProducts(BathStyle|int $bathStyleOrKey): array
    {
        $products = [];
        if (is_numeric($bathStyleOrKey)) {
            $bathStyleOrKey = BathStyle::findOne($bathStyleOrKey);
        }
        if ($bathStyleOrKey) {
            $productsId = $bathStyleOrKey->products_id;
            if (!empty($productsId)) {
                $products = Product::find()->where(['id' => $productsId])->findGroupBy('category_id');
            }
        }
        return $products;
    }

    /**
     * Возвращает массив категорий индексированных по id для переданного стиля или товаров
     * @param BathStyle|int|null $bathStyle
     * @param array|null $products
     * @return ProductCategory[]||[]
     */
    public static function getProductsCategory(BathStyle|int $bathStyle = null, array $products = null): array
    {
        $categoriesId = [];
        $categories = [];
        if (!$products && !is_null($bathStyle)) {
            //если передан id вместо стиля, тогда полаем этот стиль
            if (is_numeric($bathStyle)) {
                $bathStyle = BathStyle::findOne($bathStyle);
            }
            $productsId = $bathStyle->products_id;
            if (!empty($productsId)) {
                $products = Product::find()->where(['id' => $productsId])->indexBy()->all();
            } else {
                $products = [];
            }
        } elseif (!empty($products)) {
            foreach ($products as $productsGroup) {
                foreach ($productsGroup as $product) {
                    if ($product->category_id) {
                        $categoriesId[$product->category_id] = [$product->category_id];
                    }
                }
            }
            if (!empty($categoriesId)) {
                $categories = ProductCategory::find()->where(['id' => $categoriesId])->indexBy()->all();
            }
        }
        return $categories;
    }
}