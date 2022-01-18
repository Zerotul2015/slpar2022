<?php
////start dev statistic
//$start = microtime(true);
//$memory = memory_get_usage();

use App\Classes\MyException;
use App\Controllers\Error\Errors;

require '../vendor/autoload.php';

\App\Classes\PrepareApp::startup();
try{
$urlManager = new \App\Classes\Routing();
$urlManager->execute();
} catch (MyException $e) {
    switch ($e->getMessage()) {
//        case '404':
//            $classError = new Errors();
//            $classError->E404();
//            break;
        default:
            $classError = new Errors();
            $classError->E404();
            break;
    }
}



//end dev statistic
//echo '<br>===============================================================';
//echo '<br>Время выполнения скрипта: ' . (microtime(true) - $start) . ' sec.';
//echo '<br>Использовано памяти: ' . (memory_get_usage() - $memory)/1024 . ' Кбайт';