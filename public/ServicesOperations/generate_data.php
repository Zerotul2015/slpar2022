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
$faker = Faker\Factory::create('ru_RU');

//категории
//for ($i =1; $i < 2; $i++){
//    $category = ActiveRecord\Tables\ProductsCategory::create();
//    $category->name =$faker->text(64);
//    $category->description =$faker->text(200);
//    $category->title = $category->name;
//    $category->seo = ['title'=>'','description'=>''];
//    $category->image =$faker->image(ROOT_DIRECTORY_PUBLIC . DIRECTORY_SEPARATOR . 'upload/temp', 640, 480);
//    \App\Model\Admin\Products\ProductsCategoryModel::SaveCategory($category->toArray());
//}
//die;
//продукты
for ($i =1; $i < 47; $i++){
    $products = ActiveRecord\Tables\Products::create();
    $products->unit =2;
    $products->manufacturer =1;
    $products->stock_status =2;
    $products->name =$faker->text(64);
    $products->category =151;
    $products->description =$faker->text(200);
    $products->folder = $faker->uuid();
    \App\Model\Admin\Products\ProductsModel::Save($products->toArray());
}


