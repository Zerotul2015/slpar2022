<?php


namespace App\Model;


use App\Classes\ActiveRecord\Main;
use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\GalleryCategory;
use App\Classes\ActiveRecord\Tables\Menu;
use App\Classes\ActiveRecord\Tables\Page;
use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\ActiveRecord\Tables\Settings;
use App\Classes\ActiveRecord\Tables\SettingsBanner;
use App\Classes\ActiveRecord\Tables\SettingsIndexPage;
use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Classes\ActiveRecord\Tables\SettingsSections;

use App\Classes\Breadcrumbs;
use App\Classes\MyException;
use JetBrains\PhpStorm\ArrayShape;


class PublicTemplateModel
{


    /**
     * Основной метод получения настроек макета, меню, seo и пр. для указаной страницы
     * @param string $section
     * @param int|string $indexOrUrlObject
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "array"])]
    public static function templateSettings(string $section = '', int|string $indexOrUrlObject =''): array
    {
        if (is_numeric($indexOrUrlObject)) {
            $where = 'id';
        } else {
            $where = 'url';
        }
        $breadcrumb = new Breadcrumbs('Главная', '/');
        $objectData = null;
        $sectionCheck = ['page' => true, 'pageCategory' => true, 'product' => true, 'productCategory' => true, 'cart' => true, 'compare' => true, 'bathStyle' => true];
        if (isset($sectionCheck[$section])) {
            $objectData = match ($section) {
                'page' => Page::find()->where([$where => $indexOrUrlObject])->one(),
                'pageCategory' => PageCategory::find()->where([$where => $indexOrUrlObject])->one(),
                'product' => Product::find()->where([$where => $indexOrUrlObject])->one(),
                'productCategory' => ProductCategory::find()->where([$where => $indexOrUrlObject])->one(),
                'cart' => $breadcrumb->add('Корзина', '/cart')->render(),
                'compare' => $breadcrumb->add('Сравнение товаров', '/compare')->render(),
                'favorite' => $breadcrumb->add('Избранное', '/favorite')->render(),
                'bathStyle' => BathStyle::find()->where([$where => $indexOrUrlObject])->one(),
            };
        }
        $breadcrumb = self::generateBreadcrumb($objectData);

        $footer = PublicTemplateModel::prepareFooter();
        //меню
        $menuCategory = PublicTemplateModel::getMenuCategory();

        //SEO
        $seo = static::getSeo($section, $objectData);

        $returnData = [
            'breadcrumb' => $breadcrumb,
            'bathStyles' => BathStyle::find()->indexBy()->all(),
            'menuCatalog' => $menuCategory,
            'footer' => $footer,
            'seo' => static::getSeo($section, $objectData),
        ];
        $result = true;

        return ['result' => $result, 'returnData' => $returnData];

    }


    /**
     * Возвращает подготоваенный массив данных для вывода в подвале сайта
     * @param Settings|array|null $settings
     * @return array
     */
    public static function prepareFooter(Settings|array $settings = null): array
    {
        $footerPrepared = [];
        if(is_array($settings)){
            $footerRaw = $settings;
        }elseif (is_null($settings)){
            $settings = Settings::findOne(1);
            $footerRaw = $settings->template_footer;
        }else{
            $footerRaw = $settings->template_footer;
        }

        $pagesId = [];
        foreach ($footerRaw as $keyCol => $footerCol) {
            if ($footerCol['type'] === 'pages') {
                $pagesId = array_replace($pagesId, $footerCol['values']);
            }
        }

        $pages = Page::find()->where(['id' => $pagesId])->select(['title', 'url', 'integrated', 'url_integrated'])->indexBy()->all();
        foreach ($footerRaw as $keyCol => $footerCol) {
            $footerPrepared[$keyCol] = [];
            $footerPrepared[$keyCol]['type'] = $footerCol['type'];
            $footerPrepared[$keyCol]['values'] = [];
            if ($footerCol['type'] === 'pages') {
                foreach ($footerCol['values'] as $keyPage => $pageCurrentId) {
                    if (isset($pages[$pageCurrentId])) {
                        $footerPrepared[$keyCol]['values'][$keyPage] = $pages[$pageCurrentId];
                    }
                }
            } else {
                $footerPrepared[$keyCol]['values'] = $footerCol['values'];
            }
        }

        return $footerPrepared;
    }


    /**
     * Возвращает SEO заголовки для указанного объекта
     * @param string $section
     * @param Main|null|false $objectData
     * @return string[]
     */
    #[ArrayShape(['title' => "string", 'description' => "string"])]
    public static function getSeo(string $section, Main|null|false $objectData = null): array
    {
        $seo = [
            'title' => '',
            'description' => '',
        ];
        $settings = Settings::findOne(1);
        switch ($section) {
            case 'page':
                $seo['title'] = empty($objectData->seo['title']) ? $objectData->title : $objectData->seo['title'];
                $seo['description'] = empty($objectData->seo['description']) ? '' : $objectData->seo['description'];
                break;
            case 'pageCategory':
                $seo['title'] = empty($objectData->seo['title']) ? $objectData->name_full : $objectData->seo['title'];
                $seo['description'] = empty($objectData->seo['description']) ? '' : $objectData->seo['description'];
                break;
//Используется default
//            case 'product':
//                break;
//            case 'productCategory':
//                break;
//            case 'bathStyle':
//                break;
            case 'cart':
                $seo['title'] = 'Корзина';
                break;
            case 'compare':
                $seo['title'] = 'Сравнене товаров';
                break;
            case 'favorite':
                $seo['title'] = 'Избранные товары';
                break;
            default:
                if ($objectData) {
                    if (empty($objectData->seo['title'])) {
                        $seo['title'] = $objectData->name ?? '';
                    } else {
                        $seo['title'] = $objectData->seo['title'];
                    }
                    $seo['description'] = empty($objectData->seo['description']) ? '' : $objectData->seo['description'];
                }
                break;

        }
        //добавляем префикс и постфикс
        if ($section === 'product' || $section === 'productCategory') {
            $seo['title'] = $settings->title_prefix_product . $seo['title'] . $settings->title_postfix_product;
        } else {
            $seo['title'] = $settings->title_prefix . $seo['title'] . $settings->title_postfix;
        }
        return $seo;
    }

    /**
     * Возвращает html код хлебных крошек.
     * Вернет пустую строку если не передать $currentObj или передать NULL|false(сделано что бы не городить проверки везде где использую)
     *
     * @param Page|Product|ProductCategory|PageCategory|BathStyle|Main|null|false $currentObj
     * @return string
     */
    public static function generateBreadcrumb(Page|Product|ProductCategory|PageCategory|BathStyle|Main|null|false $currentObj = null): string
    {
        $breadCrumb = '';
        if ($currentObj) {
            $nameClass = get_class($currentObj);
            switch ($nameClass) {
                case ProductCategory::class:
                    if ($currentObj->parent_id) {
                        if ($categoryParent = ProductCategory::findOne($currentObj->parent_id)) {
                            $breadCrumbClass = new Breadcrumbs('Главная', '/');
                            $breadCrumbClass->add('Каталог', "/");
                            $breadCrumbClass->add($categoryParent->name, "/catalog/$categoryParent->url");
                            $breadCrumbClass->add($currentObj->name, "/catalog/$currentObj->url");
                            $breadCrumb = $breadCrumbClass->render();
                        }
                    } else {
                        $breadCrumbClass = new Breadcrumbs('Главная', '/');
                        $breadCrumbClass->add('Каталог', "/");
                        $breadCrumbClass->add($currentObj->name, "/catalog/$currentObj->url");
                        $breadCrumb = $breadCrumbClass->render();
                    }
                    break;
                case Product::class;
                    if ($categoryParent = ProductCategory::findOne($currentObj->category_id)) {
                        $breadCrumbClass = new Breadcrumbs('Главная', '/');
                        if ($categoryParent->parent_id) {
                            if ($categoryParent2 = ProductCategory::findOne($categoryParent->parent_id)) {
                                if ($categoryParent2->parent_id) {
                                    if ($categoryParent3 = ProductCategory::findOne($categoryParent2->parent_id)) {
                                        $breadCrumbClass->add($categoryParent3->name, "/catalog/$categoryParent3->url");
                                    }
                                }
                                $breadCrumbClass->add($categoryParent2->name, "/catalog/$categoryParent2->url");
                            }
                        }
                        $breadCrumbClass->add($categoryParent->name, "/catalog/$categoryParent->url");
                        $breadCrumbClass->add($currentObj->name, "/product/$currentObj->url");
                        $breadCrumb = $breadCrumbClass->render();
                    }
                    break;
                case BathStyle::class;
                    $breadCrumbClass = new Breadcrumbs('Главная', '/');
                    $breadCrumbClass->add('Стилевые решения', "/bath-style");
                    $breadCrumbClass->add($currentObj->name, "/bath-style/$currentObj->url");
                    $breadCrumb = $breadCrumbClass->render();
                    break;
                default:
                    break;
            }
        }
        return $breadCrumb;
    }

    /**
     * @return ProductCategory[]|NULL
     */
    public static function getMenuCategory(): ?array
    {

        $categoriesReturn = [];
        $categoryMain = ProductCategory::find()->where('parent_id', 0)->indexBy()->all();
        if (!empty($categoryMain) && is_array($categoryMain)) {
            $categoryChild = ProductCategory::find()->where(['parent_id' => array_keys($categoryMain)])->indexBy()->all();
            if (!empty($categoryChild) && is_array($categoryChild)) {
                $categories = $categoryMain + $categoryChild;
            } else {
                $categories = $categoryMain;
            }
            foreach ($categories as $categoryID => $category) {
                if ($category->parent_id) {
                    $categoriesReturn[$category->parent_id][] = $category;
                } else {
                    $categoriesReturn[0][] = $category;
                }
            }
        }
        // сортируем по приоритету
        foreach ($categoriesReturn as $catParentID => $catArrays) {
            usort($catArrays, function ($a, $b) {
                return strcmp($b->priority, $a->priority);
            });
            $categoriesReturn[$catParentID] = $catArrays;
        }
        return $categoriesReturn;
    }

    /**
     * Генерирует html код для Open Graph
     * @param string $title
     * @param string $type
     * @param string|array $image
     * @param string $description
     * @return string
     */
    public static function generateOpenGraph($title = 'Камин42', $type = 'article', $image = '', $description = ''): string
    {
        $htmlMeta = '<meta property="og:type" content="' . $type . '" /> 
                     <meta property="og:url" content="' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '"/>
                     <meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES | ENT_HTML5) . '"/>
                     <meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES | ENT_HTML5) . '"/>';

        $htmlMetaImage = '';
        if (is_array($image)) {
            foreach ($image as $key => $imageOne) {
                $htmlMetaImage = $htmlMetaImage . '<meta property="og:image" content="' . $imageOne . '"/>';
            }
        } elseif (!empty($image)) {
            $htmlMetaImage = '<meta property="og:image" content="' . $image . '"/>';
        }

        return $htmlMeta . $htmlMetaImage;
    }
}