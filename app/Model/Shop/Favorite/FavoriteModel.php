<?php

namespace App\Model\Shop\Favorite;

use App\Classes\ActiveRecord\Tables\Favorite;
use App\Classes\ActiveRecord\Tables\Product;
use JetBrains\PhpStorm\ArrayShape;

class FavoriteModel
{
    public static function getFavorite(bool $skipSave = false): Favorite
    {
        $favorite = Favorite::find()->where(['sid' => session_id()])->one();
        if (!$favorite) {
            $favorite = Favorite::create(['sid' => session_id()]);
            $favorite->save();
        }
        $favorite->products = self::getProductsForFavorite($favorite->products);
        if (!$skipSave) {
            self::saveFavorite($favorite);
        }
        return $favorite;
    }

    /**
     * Возвращает массив с данными товаров или пустой массив если таких товаров нет в базе.
     * @param $productsInFavorite // [id=>id,...]
     * @return array
     */
    private static function getProductsForFavorite($productsInFavorite): array
    {
        $returnProducts = [];
        if (!empty($productsInFavorite) && is_array($productsInFavorite)) {
            //получаем из базы все товары присутствущие в корзине
            $keysProductsInFavorite = array_keys($productsInFavorite);
            $productsInBase = Product::find()->where(['id' => $keysProductsInFavorite])->indexBy()->all();
            $returnProducts = $productsInBase ?: [];
        }
        return $returnProducts;
    }

    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Favorite"])]
    public static function addProduct(int $productId): array
    {
        $result = false;
        $favorite = static::getFavorite(true);
        $productsInFavorite = $favorite->products;
        if (Product::findOne($productId)) {
            $productsInFavorite[$productId] = $productId;
            $result = true;
        }
        $favorite->products = $productsInFavorite;
        static::saveFavorite($favorite);
        return ['result' => $result, 'returnData' => $favorite];
    }

    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Favorite"])]
    public static function removeProduct(mixed $productId): array
    {
        $favorite = static::getFavorite(true);
        $products = $favorite->products;
        if (isset($products[$productId])) {
            unset($products[$productId]);
            $favorite->products = $products;
            static::saveFavorite($favorite);
        }
        return ['result' => true, 'returnData' => $favorite];
    }

    public static function deleteFavorite(): bool
    {
        $favorite = static::getFavorite();
        return $favorite->del();
    }

    private static function saveFavorite(Favorite $favoriteAfterGet): void
    {
        $favoriteProducts = $favoriteAfterGet->products;
        if (!empty($favoriteProducts)) {
            $favoriteProductCleaner = [];
            foreach ($favoriteProducts as $favoriteProductItem) {
                $favoriteProductCleaner[$favoriteProductItem->id] = $favoriteProductItem->id;
            }
            $favoriteAfterGet->products = $favoriteProductCleaner;
        }
        $favoriteAfterGet->save();
    }
}