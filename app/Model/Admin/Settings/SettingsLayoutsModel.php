<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 22.08.18
 * Time: 19:34
 */

namespace App\Model\Admin\Settings;


use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Model\Interfaces\DefaultMethodTableClass;

class SettingsLayoutsModel implements DefaultMethodTableClass
{
    public static function Save($val)
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        /*
            layout_for
            1: 'Категории товаров',
            2: 'Подборки товаров',
            3: 'Страницы',
        */
        //проверка
        $layoutForCheck = [1 => 1, 2 => 2, 3 => 3];
        if (isset($layoutForCheck[$val['layout_for']])) {
            $val['widgets'] = static::checkWidgets($val['blocks']);
            if ($val['is_default'] == 1) {
                $defaultOld = SettingsLayouts::find()->where(['is_default' => 1, 'layout_for' => $val['layout_for']]);
                $layoutForSave = $defaultOld->set($val);
            } else {
                $layoutForSave = SettingsLayouts::create($val);
            }
            if($returnResult['result'] = boolval($layoutForSave->save())){
                $returnResult['id'] = $layoutForSave->id;
                $returnResult['returnData'] = $layoutForSave;
            }
        }
        return $returnResult;
    }

    /**
     *
     * Проверка стрктуры виджетов, и удаление пустых элементов и строк
     * @param $blocks
     * @return array
     */
    public static function checkWidgets($blocks)
    {
        /*
        1: 'Баннер',
        2: 'Подборка товара',
        3: 'Произвольный HTML-блок',
        4: 'Страница'
        */
        $typeAllowed = [1 => 1, 2 => 2, 3 => 3, 4 => 4];

        $needBlocksLocation = ['topBlocks' => [], 'sidebarBlocks' => [], 'bottomBlocks' => []];

        $blocks = array_merge($needBlocksLocation, $blocks);

        foreach ($blocks as $location => $lines) {
            foreach ($lines as $keyLine => $lineElements) {
                foreach ($lineElements as $keyElement => $element) {
                    if (isset($typeAllowed[$element['type']])) {
                        if ($element['type'] !== 3) {
                            if (!isset($element['id'])) {
                                unset($blocks[$location][$keyLine][$keyElement]);
                            }
                        } elseif ($element['type'] == 3 && (!isset($element['val']) || empty($element['val']))) {
                            unset($blocks[$location][$keyLine][$keyElement]);
                        }
                    }
                }
                //если линия оказалась пустой убираем ее.
                if (empty($lineElements)) {
                    unset($blocks[$location][$keyLine]);
                }
            }
        }
        return $blocks;
    }

    /**
     *
     * Проверка стрктуры виджетов, и удаление пустых элементов и строк
     * @param $type 1: 'Баннер', 2: 'Подборка товара', 4: 'Страница'
     * @param $idForDel int
     * @return bool
     */
    public static function removeDeletedElement($type, $idForDel)
    {
        $layouts = SettingsLayouts::findAll();

        $positionsBlocks = ['topBlocks', 'sidebarBlocks', 'bottomBlocks'];
        foreach ($layouts as $layout) {
            $blocks = $layout->blocks;
            $isChange = false;
            foreach ($positionsBlocks as $position) {
                if (!empty($blocks[$position])) {
                    foreach ($blocks[$position] as $keyRows => $rows) {
                        foreach ($rows as $keyElement => $element) {
                            if ($type == $element['type'] && $idForDel == $element['id']) {
                                $isChange = true;
                                unset($blocks[$position][$keyRows][$keyElement]);
                                if (!empty($blocks[$position][$keyRows])) {
                                    unset($blocks[$position][$keyRows]);
                                }
                            }
                        }
                    }
                }
            }
            if ($isChange) {
                $layout->blocks = $blocks;
                $layout->save();
            }
        }

        return true;
    }


    public
    static function Delete($id)
    {
        return SettingsLayouts::findOne($id)->del();
    }
}