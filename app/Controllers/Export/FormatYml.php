<?php

namespace App\Controllers\Export;

use App\Model\Admin\Export\ExportModel;

class FormatYml
{
    public function Main(): void
    {
        header('Content-Type: text/xml; charset=utf-8');
        echo file_get_contents(ExportModel::generateYml());
    }
}