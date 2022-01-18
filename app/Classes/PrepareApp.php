<?php


namespace App\Classes;


use Dotenv\Dotenv;
use \Mobile_Detect;

class PrepareApp
{

    public static function startup()
    {
        session_start();

        //define('USE_ACCESS_KEYS', false); узнат нужно ли это
        define('ROOT_DIRECTORY',  $_SERVER['DOCUMENT_ROOT'] .'/..');
        define('ROOT_DIRECTORY_PUBLIC', $_SERVER['DOCUMENT_ROOT']);
        $dotenv = Dotenv::createImmutable(ROOT_DIRECTORY);
        $dotenv->load();



        //Ключ для tinymce
        define('TINY_API_KEY', "jn70abbhp1e47ehum3uvvmd1iy75zb9n5pgbomwy21ha0vk4");
        define('TINY_CONFIG', "{
                relative_urls : false,
                height:400,
                language_url: '/build/js/tinymce/langs/ru.js',
                language: 'ru',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code wordcount '
                ],
                external_filemanager_path:\"/filemanager/\",
                filemanager_title:\"Менеджер файлов\" ,
                external_plugins: { \"filemanager\" : \"/filemanager/plugin.min.js\"},
                toolbar:[
                    'responsivefilemanager |  undo redo | formatselect | bold italic forecolor backcolor | \\
                    alignleft aligncenter alignright alignjustify | \
                    bullist numlist outdent indent | removeformat | help',
                'mybutton'],
                extended_valid_elements : 'feedback-form[*]',
                custom_elements : 'feedback-form',
                content_css: \"/build/css/admin/tiny.editor.css\",
                setup: function (editor) {
                    /* example, adding a toolbar menu button */
                    editor.ui.registry.addMenuButton('mybutton', {
                        text: 'Готовые элементы',
                        fetch: function (callback) {
                            var items = [
                                {
                                    type: 'menuitem',
                                    text: 'Форма обратной связи',
                                    onAction: function () {
                                        editor.insertContent('<feedback-form>Форма обратной связи</feedback-form>');
                                    }
                                },
                            ];
                            callback(items);
                        }
                    });

                }
            }");

//Определения типа устройства посетителя
        $detect = new Mobile_Detect();
        define('IS_MOBILE', $detect->isMobile());
        define('IS_TABLET', $detect->isTablet());

//Проверка авторизаций



// устанавливаем куки wishlist
//        $wishlistProducts = [];
//        if (!isset($_COOKIE['wishlist_token']) || empty($_COOKIE['wishlist_token'])) {
//            $token = uniqid('', true);
//            $wishlist = ProductsWishlist::create();
//            $wishlist->set(['token' => $token])->save();
//            setcookie('wishlist_token', $token, time() + 60 * 60 * 24 * 30, '/');
//        } else {
//            if (isset($_COOKIE['wishlist_token']) && !$wishlist = ProductsWishlist::find()->where(['token' => $_COOKIE['wishlist_token']])->one()) {
//                $token = uniqid('', true);
//                $wishlist = ProductsWishlist::create();
//                $wishlist->set(['token' => $token])->save();
//                setcookie('wishlist_token', $token, time() + 60 * 60 * 24 * 30, '/');
//            } else {
//                $wishlistProducts = $wishlist->products;
//                setcookie('wishlist_token', $_COOKIE['wishlist_token'], time() + 60 * 60 * 24 * 30, '/');
//            }
//        }
//        define('WISHLIST', $wishlistProducts);
    }

}