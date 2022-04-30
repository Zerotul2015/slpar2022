<?php

namespace App\Model\Shop\Compare;

use App\Classes\ActiveRecord\Tables\Compare;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use JetBrains\PhpStorm\ArrayShape;

class CompareModel
{
    public static function getCompare(bool $skipSave = false, $withCategories = false): Compare
    {
        $compare = Compare::find()->where(['sid' => session_id()])->one();
        if (!$compare) {
            $compare = Compare::create(['sid' => session_id()]);
            $compare->save();
        }
        $compare->products = self::getProductsForCompare($compare->products);
        if (!$skipSave) {
            $compare2 = clone $compare;
            self::saveCompare($compare2);
        }
        if ($withCategories) {
            $categoriesById = self::getCategoriesForCompare($compare->products);
            $compare->products = self::combineCategoriesAndProducts($compare->products, $categoriesById);
        }

        return $compare;
    }

    /**
     * @param Product[] $productsInComparePrepared
     * @param array $categoriesById
     * @return array ['catID'=>['category'=>ProductCategory, 'products'=>Product,...],...]
     */
    private static function combineCategoriesAndProducts(array $productsInComparePrepared, array $categoriesById): array
    {
        $returnData = [];
        foreach ($productsInComparePrepared as $product) {
            if ($product->category_id && isset($categoriesById[$product->category_id])) {
                $returnData[$product->category_id]['products'][] = $product;
            } else {
                $returnData[0]['products'][] = $product;
            }
        }
        foreach ($returnData as $idGroup => $groupCat) {
            if ($idGroup === 0) {
                $returnData[$idGroup]['category'] = ProductCategory::create()->name = 'Без категории';
            } else {
                $returnData[$idGroup]['category'] = $categoriesById[$idGroup];
            }
        }

        //['catID'=>['category'=>ProductCategory, 'products'=>Product,...]]
        return $returnData;
    }

    /**
     * Возвращает массив с данными товаров или пустой массив если таких товаров нет в базе.
     * @param $productsInCompare // [id=>id,...]
     * @return array
     */
    private static function getProductsForCompare($productsInCompare): array
    {
        $returnProducts = [];
        if (!empty($productsInCompare) && is_array($productsInCompare)) {
            //получаем из базы все товары присутствущие в корзине
            $keysProductsInCompare = array_keys($productsInCompare);
            $productsInBase = Product::find()->where(['id' => $keysProductsInCompare])->indexBy()->all();
            $returnProducts = $productsInBase ?: [];
        }
        return $returnProducts;
    }

    /**
     * Возвращает массив с данными категорий или пустой массив.
     * @param false|Product[]|null $productsInComparePrepared
     * @return ProductCategory[]
     */
    private static function getCategoriesForCompare(false|array|null $productsInComparePrepared): array
    {
        $returnCategories = [];
        if (!empty($productsInComparePrepared) && is_array($productsInComparePrepared)) {
            $arrayCatId = [];
            foreach ($productsInComparePrepared as $product) {
                if ($product->category_id) {
                    $arrayCatId[$product->category_id] = $product->category_id;
                }
            }
            if (!empty($arrayCatId) && $categories = ProductCategory::find()->where(['id' => $arrayCatId])->indexBy()->all()) {
                $returnCategories = $categories;
            }
        }
        return $returnCategories;
    }

    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Compare"])]
    public static function addProduct($productId): array
    {
        $result = false;
        $compare = static::getCompare(true);
        $productsInCompare = $compare->products;
        if (Product::findOne($productId)) {
            $productsInCompare[$productId] = $productId;
            $result = true;
        }
        $compare->products = $productsInCompare;
        static::saveCompare($compare);
        return ['result' => $result, 'returnData' => $compare];
    }

    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Compare"])]
    public static function removeProduct(mixed $productId): array
    {
        $compare = static::getCompare(true);
        $products = $compare->products;
        if (isset($products[$productId])) {
            unset($products[$productId]);
            $compare->products = $products;
            static::saveCompare($compare);
        }
        return ['result' => true, 'returnData' => $compare];
    }

    public static function deleteCompare(): bool
    {
        $compare = static::getCompare();
        return $compare->del();
    }

    private static function saveCompare(Compare $compareAfterGet): void
    {
        $compareProducts = $compareAfterGet->products;
        if (!empty($compareProducts)) {
            $compareProductCleaner = [];
            foreach ($compareProducts as $productId => $compareProductItem) {
                $compareProductCleaner[$productId] = $productId;
            }
            $compareAfterGet->products = $compareProductCleaner;
        }
        $compareAfterGet->save();
    }
}