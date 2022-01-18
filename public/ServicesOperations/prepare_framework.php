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
$db = new ActiveRecord();

function camelCase($str) {
    $i = array("-","_");
    $str = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $str);
    $str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
    $str = str_replace($i, ' ', $str);
    $str = str_replace(' ', '', ucwords(strtolower($str)));
    $str = strtolower(substr($str,0,1)).substr($str,1);
    return $str;
}

/**
 * Преобразовываем имя талицы к названию класса
 * @param $name (название таблицы)
 * @return string (имя класса)
 */
function classNameFromTableName($name): string
{
    $classNamePrepare = explode('_', $name);
    $className = '';
    foreach ($classNamePrepare as $classNameWord) {
        $className = $className . ucfirst($classNameWord);
    }
    return '\App\Classes\ActiveRecord\Tables\\' . $className;
}


$tableArray = $db->getTableList();

foreach ($tableArray as $tableKeyArray) {
    $uniqueKey = [];
    foreach ($tableKeyArray as $tableName) {
        $classNamePrepare = explode('_', $tableName);
        $nameClass = '';
        foreach ($classNamePrepare as $classNameWord) {
            $nameClass = $nameClass . ucfirst($classNameWord);
        }
        // получаем список полей у таблицы
        $columnName = $db->getNameColumn($tableName);
        $phpDocBlockStart = "/**\n";
        $phpDocBlockEnd = "\n */";
        $phpDocBlockParamArray = [];
        $columnNameArray = [];



        $primaryKey = '';
        foreach ($columnName as $param => $detailField) {

            $uniqueKeyBlock = '[]';
            //проверка на PrimaryKey
            if($detailField['key'] == 'PRI'){
                $primaryKey = $detailField['field'];
            }
            if ($detailField['key'] == 'UNI') {
                $uniqueKey[] = $detailField['field'];
            }
            //массив значений для phpdoc
            $phpDocBlockParamArray[] = " * @property mixed $param regular read/write property";
            $paramCameCase = camelCase($param);
            if($param !== $paramCameCase) {
                $phpDocBlockParamArray[] = " * @property mixed $paramCameCase regular read/write property";
            }
            // массив значений для $columnName
            $field = $detailField['field'];
            if (empty($field)) {
                $field = "''";
            } else {
                $field = "'" . $field . "'";
            }
            $type = $detailField['type'];
            if (empty($type)) {
                $type = "''";
            } else {
                $type = "'" . $type . "'";
            }
            $null = $detailField['null'];
            if (empty($null)) {
                $null = "''";
            } else {
                $null = "'" . $null . "'";
            }
            $key = $detailField['key'];
            if (empty($key)) {
                $key = "''";
            } else {
                $key = "'" . $key . "'";
            }

            $default = $detailField['default'];
            if (empty($default)) {
                if (is_null($default)) {
                    $default = 'null';
                } else {
                    $default = "''";
                }
            } else {
                $default = "'" . $default . "'";
            }

            $extra = $detailField['extra'];
            if (empty($extra)) {
                $extra = "''";
            } else {
                $extra = "'" . $extra . "'";
            }

            $columnNameArray[] = <<<EOD
        $field => [
            'field' => $field,
            'type' => $type,
            'null' => $null,
            'key' => $key,
            'default' => $default,
            'extra' => $extra
        ]
EOD;
        }
        $phpDocBlock = $phpDocBlockStart . implode("\n", $phpDocBlockParamArray) . $phpDocBlockEnd;
        $columnNameBlock = "[\n" . implode(",\n", $columnNameArray) . "\n   ]";
        if(empty($uniqueKey) == false){
            $arrayUniKey = [];
            foreach ($uniqueKey as $uKey){
                $arrayUniKey[] = "'" . $uKey . "' => ''";
            }
            unset($uKey);
            $uniqueKeyBlock = '[' . implode(',', $arrayUniKey) . ']';
        }
        $uniqueKey =[];
    }
    $className =classNameFromTableName($tableName);

    $textInsertFileClass = "<?php \n
namespace App\Classes\ActiveRecord\Tables;\n
use App\Classes\ActiveRecord\Main; \n
$phpDocBlock\n
class $nameClass extends Main
{
    static string \$tableName = '$tableName'; \n
    static string \$className = '$className'; \n
    static array \$columnName = $columnNameBlock; \n
    static string \$primaryKey = '$primaryKey';
    static array  \$uniqueKey = $uniqueKeyBlock; \n
}";

    $pathTableClass = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .  '..'. DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'ActiveRecord' . DIRECTORY_SEPARATOR . 'Tables' . DIRECTORY_SEPARATOR . $nameClass . '.php';

    if(isset($_GET['rewrite']) && $_GET['rewrite'] == 1){
        if (!file_put_contents($pathTableClass, $textInsertFileClass)) {
            $error[] = 'Не удалось создать ' . $pathTableClass;
        }
    }else{
        if (file_exists($pathTableClass)) {
            echo 'Класс ' . $nameClass . '.php' . ' существует<br>';
            continue;
        } else {
            if (!file_put_contents($pathTableClass, $textInsertFileClass)) {
                $error[] = 'Не удалось создать ' . $pathTableClass;
            }
        }
    }


}
if (empty($error)) {
    echo 'Все классы для таблиц из базы успешно созданы.';
}


echo '<p>---------------------------------------------</p>';
echo 'Создаем папки для ImageCache:<br>';
$settings = ImageCache::$settings;

$countCreateFiles = 0;
$countCreateFolder = 0;
$fileSystem = new FilesystemModel();
foreach ($settings['path'] as $path) {
    if (!$fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . '/index.html')) {
        $fileSystem->dumpFile(ROOT_DIRECTORY_PUBLIC . $path . '/index.html', 'access denied');
        echo 'Файл ' . ROOT_DIRECTORY_PUBLIC . $path . 'index.html' . ' создан<br>';
    }
    foreach ($settings['templates'] as $pathTemplate => $with) {
        if (!$fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate)) {
            $fileSystem->mkdir(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate, 0755);
            echo 'Путь ' . ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . ' создан<br>';
            $countCreateFolder++;
        }
        if (!$fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . '/index.html')) {
            $fileSystem->dumpFile(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . '/index.html', 'access denied');
            echo 'Файл ' . ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . 'index.html' . ' создан<br>';
            $countCreateFiles++;
        }
    }
}
echo "<i>Создано $countCreateFolder папок и $countCreateFiles файлов</i><br>";
echo '<p>---------------------------------------------</p>';
