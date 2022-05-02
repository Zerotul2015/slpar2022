<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 27.10.18
 * Time: 21:18
 */

namespace App\Model\Shop\Cart;


use App\Classes\ActiveRecord\Tables\Cart;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\PromoCode;
use JetBrains\PhpStorm\ArrayShape;

class CartModel
{
    /**
     * Возвращает объект корзины из базы
     * @param bool $skipSave
     * @return Cart
     */
    public static function getCart(bool $skipSave = false): Cart
    {
        $cart = Cart::find()->where(['sid' => session_id()])->one();
        if (!$cart) {
            $cart = Cart::create(['sid' => session_id()]);
            $cart->save();
        }
        //В базе хранится в виде products [id=>count,...]
        //на выходе получим products [id=>['count'=>3, 'product'=>Product], ...]
        $cart->products = self::getProductsDataForCart($cart->products);
        $promoCode = $cart->promo_code_used ? PromoCode::findOne($cart->promo_code_used) : null;
        if ($promoCode && self::checkActivePromoCode($promoCode)) {
            $cart->promo_code_used = $promoCode;
        } else {
            $cart->promo_code_used = null;
        }
        if (!$skipSave) {
            self::saveCart($cart);
        }
        return $cart;
    }

    /**
     * Используется получения даных корзины и приведение в актуальность сохраняем ее.
     * Принимает Cart уже проверенный и подготовленный к выводу на сайте.
     * @param Cart $cartAfterGet
     * @return void
     */
    private static function saveCart(Cart $cartAfterGet): void
    {
        $cartAfterGet2 = clone $cartAfterGet; // чтобы не испортить подготовленные к выводу в шаблон данные о товарах
        $cartProducts = $cartAfterGet2->products;
        if (!empty($cartProducts)) {
            $cartProductCleaner = [];
            foreach ($cartProducts as $productId => $cartProductItem) {
                $cartProductCleaner[$productId] = $cartProductItem['count'];
            }
            $cartAfterGet2->products = $cartProductCleaner;
        }
        $cartPromoCode = $cartAfterGet2->promo_code_used;
        if ($cartPromoCode) {
            $cartAfterGet2->promo_code_used = $cartPromoCode->id;
        }
        $cartAfterGet2->save();
    }


    /**
     * Возвращает массив с данными товаров или пустой массив если таких товаров нет в базе.
     * @param $productsInCart // [id=>count,...]
     * @return array
     */
    private static function getProductsDataForCart($productsInCart): array
    {
        $returnProducts = [];
        if (!empty($productsInCart) && is_array($productsInCart)) {
            //получаем из базы все товары присутствущие в корзине
            $keysProductsInCart = array_keys($productsInCart);
            $productsInBase = Product::find()
                ->where(['id' => $keysProductsInCart])
                ->select([
                    'id',
                    'name',
                    'image_main',
                    'folder',
                    'stock_status_id',
                    'url',
                    'price',
                    'price_old',
                    'price_on_request',
                    'bath_style_id'
                ])
                ->all();
            if ($productsInBase) {
                foreach ($productsInBase as $product) {
                    $returnProducts[$product->id] = ['count' => $productsInCart[$product->id], 'product' => $product];
                }

            }
        }
        return $returnProducts;
    }

    public static function checkActivePromoCode(PromoCode $promoCode): bool
    {
        $active = false;
        $timeNow = time();
        $timeStart = $timeNow;
        $timeEnd = $timeNow;
        if ($promoCode->date_start) {
            $timeStart = strtotime($promoCode->date_start);
        }
        if ($promoCode->date_start) {
            $timeEnd = strtotime($promoCode->date_end);
        }
        if (($timeStart > !$timeNow) && ($timeNow > !$timeEnd)) {
            $active = true;
        }

        return $active;
    }

    /**
     * Добавление товара в корзину
     * * Возвращает результат применения, и корзину.
     * @param int $id
     * @param int $count
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Cart"])]
    public static function addProduct(int $id, int $count = 1): array
    {
        $result = false;
        $cart = static::getCart(true);
        $productsInCart = $cart->products;
        if ($product = Product::findOne($id)) {
            if (isset($productsInCart[$id])) {
                $productsInCart[$id]['count'] += $count;
            } else {
                $productsInCart[$id] = ['count' => $count, 'product' => $product];
            }
            $result = true;
        }
        $cart->products = $productsInCart;
        static::saveCart($cart);
        return ['result' => $result, 'returnData' => $cart];
    }

    /**
     * Изменяем количество
     * Возвращает результат применения, и корзину.
     * @param $newCount
     * @param $idProduct
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Cart"])]
    public static function changeCount($idProduct, $newCount, $skip=true): array
    {
        $result = false;
        $newCount = (int)$newCount;
        if ($newCount < 1) {
            $newCount = 1;
        }
        $cart = static::getCart(true);
        $productsInCart = $cart->products;
        if (isset($productsInCart[$idProduct])) {
            $productsInCart[$idProduct]['count'] = $newCount;
            $cart->products = $productsInCart;
            static::saveCart($cart);
            $result = true;
        }
        return ['result' => $result, 'returnData' => $cart];
    }

    /**
     * Удаляем товар из корзины
     * Возвращает результат применения, и корзину.
     * @param $idProduct
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Cart"])]
    public static function removeProduct($idProduct): array
    {
        $cart = static::getCart(true);
        $products = $cart->products;
        if (isset($products[$idProduct])) {
            unset($products[$idProduct]);
            $cart->products = $products;
            static::saveCart($cart);
        }
        return ['result' => true, 'returnData' => $cart];
    }


    /**
     * Применяем промокод.
     * Возвращает результат применения, и корзину.
     * @param string $promoCode
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Tables\Cart|null"])]
    public static function applyPromoCode(string $promoCode): array
    {
        $returnData = ['result' => false, 'returnData' => null];
        $promoCodeInBase = PromoCode::find()->where(['code_text' => $promoCode])->one();
        if ($promoCodeInBase && static::checkActivePromoCode($promoCodeInBase)) {
            $cart = self::getCart(true);
            $cart->promo_code_used = $promoCodeInBase;
            static::saveCart($cart);
            $returnData = ['result' => true, 'returnData' => $cart];
        }
        return $returnData;
    }

    public static function makingOrder($orderDetails){

    }

    /**
     * Очищает корзину
     * @return bool
     */
    public static function deleteCart(): bool
    {
        $cart = static::getCart();
        return $cart->del();
    }
}