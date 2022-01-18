<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 08.03.18
 * Time: 18:06
 */

// Данный файл создает файлы классов для всех таблиц из базы

use App\Classes\ActiveRecord;
use App\Controllers\Images\ImageCache;
use App\Model\FilesystemModel;

require '../../vendor/autoload.php';
\App\Classes\PrepareApp::startup();

$error = [];

//продукты
for ($products = ActiveRecord\Tables\Products::find()->limit([0,30])->all(); !empty($products); $products = ActiveRecord\Tables\Products::find()->limit([0,30])->all()){
    if($products){
        foreach ($products as $product){
            $product->del();
        }
    }
}

//категории товаров
for ($categories = ActiveRecord\Tables\ProductsCategory::find()->limit([0,30])->all(); !empty($categories); $categories = ActiveRecord\Tables\ProductsCategory::find()->limit([0,30])->all()){
    if($categories){
        foreach ($categories as $category){
            $category->del();
        }
    }
}

//Характеристики товаров
for ($data = ActiveRecord\Tables\ProductsCharacteristicsList::find()->limit([0,30])->all(); !empty($data); $data = ActiveRecord\Tables\ProductsCharacteristicsList::find()->limit([0,30])->all()){
    if($data){
        foreach ($data as $dataItem){
            $dataItem->del();
        }
    }
}

//Подборки товаров
for ($data = ActiveRecord\Tables\ProductsCollections::find()->limit([0,30])->all(); !empty($data); $data = ActiveRecord\Tables\ProductsCollections::find()->limit([0,30])->all()){
    if($data){
        foreach ($data as $dataItem){
            $dataItem->del();
        }
    }
}

//Подготовлены фильтры по товарам
for ($data = ActiveRecord\Tables\ProductsFiltersPrepared::find()->limit([0,30])->all(); !empty($data); $data = ActiveRecord\Tables\ProductsFiltersPrepared::find()->limit([0,30])->all()){
    if($data){
        foreach ($data as $dataItem){
            $dataItem->del();
        }
    }
}