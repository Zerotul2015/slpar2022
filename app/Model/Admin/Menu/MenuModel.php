<?php


namespace App\Model\Admin\Menu;


use App\Classes\ActiveRecord\Tables\Menu;
use App\Classes\ActiveRecord\Tables\Page;
use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Model\Interfaces\DefaultMethodTableClass;

class MenuModel implements DefaultMethodTableClass
{

    public static function Save($val)
    {
        $result = false;
        if (is_array($val) && !empty($val)) {
            if (isset($val['id']) && $menu = Menu::findOne($val['id'])) {
                $menu = $menu->set($val);
            } else {
                $menu = Menu::create()->set($val);
            }
            $typePosition = [
                'header' => 'в шапке сайта',
            ];
            if (isset($typePosition[$menu->position])) {
                $menu->items = static::filterItems($menu->items);
                $result = $menu->save();
            }
        }

        return $result;
    }

    public static function Delete($id): bool
    {
        $result = false;
        if ($menu = Menu::findOne($id)) {
            $result = $menu->del();
        }
        return $result;
    }

    /**
     * Проверят пункты меню на присутствие битых ссылкок на типы данных из базы
     * @param $items
     * @return array|mixed
     */
    public static function filterItems($items)
    {
        $typeAllowed = [
            'custom' => 'Ссылка',
            'page' => 'id страницы',
            'pageCategory' => 'id категории страниц',
        ];
        $idPages = Page::find()->select('id')->indexBy()->all();
        $idPageCategories = PageCategory::find()->indexBy()->select('id')->all();
        $itemsFiltered = [];
        if (is_array($items) && !empty($items)) {
            foreach ($items as $key => $item) {
                //item = [title:'Страница 15', typeItem:'page', value':15, children:null]
                $forAddFiltered = null;
                if (isset($item['title'], $item['typeItem'], $typeAllowed[$item['typeItem']])
                    && ((isset($item['value']) && $item['value']) || $item['typeItem'] === 'custom')) {
                    switch ($item['typeItem']) {
                        case 'page':
                            if (isset($idPages[$item['value']])) {
                                $forAddFiltered = $item;
                            }
                            break;
                        case 'pageCategory':
                            if (isset($idPageCategories[$item['value']])) {
                                $forAddFiltered = $item;
                            }
                            break;
                        case 'custom':
                            $forAddFiltered = $item;
                            break;
                        default:
                            break;
                    }
                }
                if ($forAddFiltered) {
                    if (isset($forAddFiltered['children'])) {
                        $forAddFiltered['children'] = static::filterItems($forAddFiltered['children']);
                    }
                    $itemsFiltered[] = $forAddFiltered;
                }
            }
        }
        $items = !empty($itemsFiltered) ? $itemsFiltered : null;
        return $items;
    }
}