<?php


namespace App\Model;


use App\Classes\ActiveRecord\Main;
use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Menu;
use App\Classes\ActiveRecord\Tables\Page;
use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\ActiveRecord\Tables\Settings;

use App\Classes\Breadcrumbs;
use App\Classes\MyException;
use JetBrains\PhpStorm\ArrayShape;


class PublicTemplateModel
{


    /**
     * Основной метод получения настроек макета, меню, seo и пр. для указаной страницы
     * @param string $section index,page,pageCategory,product,productCategory,cart,compare,favorite,bathStyle
     * @param int|string $indexOrUrlObject
     * @param bool $simple //только seo и breadcrumb
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "array"])]
    public static function templateSettings(string $section = '', int|string $indexOrUrlObject = '', bool $simple = false): array
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
        $returnData = [];
        //если нужны все данные(например для первой загрузки или пререндера)
        if ($simple === false) {
            $footer = static::prepareFooter();
            //меню
            $menuCategory = static::getMenuCategory();
            //Меню страниц
            $menuHeader = static::getMenu('header');
            $returnData = [
                'menuCatalog' => $menuCategory,
                'menuHeader' => $menuHeader,
                'footer' => $footer,
            ];
        };

        $returnData['breadcrumb'] = static::generateBreadcrumb($objectData);
        $returnData['seo'] = static::getSeo($section, $objectData);

        return ['result' => true, 'returnData' => $returnData];
    }


    /**
     * Возвращает подготоваенный массив данных для вывода в подвале сайта
     * @param Settings|array|null $settings
     * @return array
     */
    public static function prepareFooter(Settings|array $settings = null): array
    {
        $footerPrepared = [];
        if (is_array($settings)) {
            $footerRaw = $settings;
        } elseif (is_null($settings)) {
            $settings = Settings::findOne(1);
            $footerRaw = $settings->template_footer;
        } else {
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
            case 'index':
                $seo['title'] = $settings->seo_index_page['title'];
                $seo['description'] = $settings->seo_index_page['description'];
                break;
            default:
                if ($objectData) {
                    if (empty($objectData->seo['title'])) {
                        $seo['title'] = $objectData->name ?? $settings->seo_index_page['title'];
                    } else {
                        $seo['title'] = $objectData->seo['title'];
                    }
                    $seo['description'] = empty($objectData->seo['description']) ? $settings->seo_index_page['description'] : $objectData->seo['description'];
                }
                break;
        }
        //добавляем префикс и постфикс
        if ($section === 'product' || $section === 'productCategory') {
            $seo['title'] = $settings->title_prefix_product . ' ' . $seo['title'] . ' ' . $settings->title_postfix_product;
        } elseif ($section !== 'index') {//для главной префиксы не добавляем
            $seo['title'] = $settings->title_prefix . ' ' . $seo['title'] . ' ' . $settings->title_postfix;
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
     * Возвращате Массив пунктов меню для указаной позиции
     * @param string $position
     * @return array|null
     */
    public static function getMenu(string $position): array|null
    {
        $positionCheck = ['header' => 'header'];
        if (isset($positionCheck[$position])) {
            $menu = Menu::find()->where(['position' => $position])->one();
        } else {
            $menu = null;
        }
        return static::getMenuData($menu)->items;
    }


    /**
     * Возвращате переданное меню с подставленными ссылками вместо ключей
     * @param Menu $menu
     * @return Menu
     */
    public static function getMenuData(Menu $menu): Menu
    {
        $items = $menu->items;
        $preparedMenuItems = [];
        if (!empty($items) && is_array($items)) {
            $pageIdArray = static::getPagesIdFromMenuItems($items);
            $pageCategoryIdArray = static::getPageCategoryIdFromMenuItems($items);

            if (!empty($pageIdArray)) {
                $pages = Page::find()->where(['id' => $pageIdArray])
                    ->select(['id', 'title', 'url', 'integrated', 'url_integrated'])
                    ->indexBy()
                    ->all();
            } else {
                $pages = [];
            }
            if (!empty($pageCategoryIdArray)) {
                $pageCategories = PageCategory::find()->where(['id' => $pageCategoryIdArray])
                    ->select(['id', 'name_short', 'name_full', 'url'])
                    ->indexBy()
                    ->all();
            } else {
                $pageCategories = [];
            }
            $preparedMenuItems = static::prepareLinkForMenu($items, $pages, $pageCategories);
        }

        $menu->items = $preparedMenuItems;
        return $menu;

    }

    /**
     * В переданных пунктах заменят ключи ссылками.
     * @param array $menuItems
     * @param $pages
     * @param $pageCategories
     * @return array
     */
    public static function prepareLinkForMenu(array $menuItems, $pages, $pageCategories): array
    {
        $preparedMenu = [];
        foreach ($menuItems as $keyItem => $item) {
            if (empty($item['value'])) {
                continue;
            } else {
                $preparedMenu[$keyItem] = $item;
                if ($item['typeItem'] === 'page' && isset($pages[$item['value']])) {
                    if ($pages[$item['value']]->integrated) {
                        $url = $pages[$item['value']]->url_integrated;
                    } else {
                        $url = '/page/' . $pages[$item['value']]->url;
                    }
                    $preparedMenu[$keyItem]['value'] = $url;
                }
                if ($item['typeItem'] === 'pageCategory' && isset($pageCategories[$item['value']])) {
                    $url = '/page-category/' . $pageCategories[$item['value']]->url;
                    $preparedMenu[$keyItem]['value'] = $url;
                }
                //рекурсивный перебор детей
                if (!empty($preparedMenu[$keyItem]['children']) && is_array($preparedMenu[$keyItem]['children'])) {
                    $preparedMenu[$keyItem]['children'] =
                        static::prepareLinkForMenu($preparedMenu[$keyItem]['children'], $pages, $pageCategories);
                }
            }
        }
        return $preparedMenu;
    }

    /**
     * Возвразает массив id страниц используемых в переданных пунктах меню
     * @param array $menuItems
     * @return array
     */
    public static function getPagesIdFromMenuItems(array $menuItems): array
    {
        $pageIdArray = [];
        foreach ($menuItems as $item) {
            if ($item['typeItem'] === 'page') {
                $pageIdArray[$item['value']] = $item['value'];
            }
            if (!empty($item['children']) && is_array($item['children'])) {
                $pageIdArray + static::getPagesIdFromMenuItems($item['children']);
            }
        }
        return $pageIdArray;
    }

    /**
     * * Возвразает массив id категорий страниц используемых в переданных пунктах меню
     * @param array $menuItems
     * @return array
     */
    public static function getPageCategoryIdFromMenuItems(array $menuItems): array
    {
        $pageCategoryIdArray = [];
        foreach ($menuItems as $item) {
            if ($item['typeItem'] === 'pageCategory') {
                $pageCategoryIdArray[$item['value']] = $item['value'];
            }
            if (!empty($item['children']) && is_array($item['children'])) {
                $pageCategoryIdArray + static::getPageCategoryIdFromMenuItems($item['children']);
            }
        }
        return $pageCategoryIdArray;
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