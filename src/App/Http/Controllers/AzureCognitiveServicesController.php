<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AzureCognitiveServicesController extends \App\Http\Controllers\Controller
{
    private $_face;

    public function face(): AzureCognitiveServiceFaceController
    {
        if($this->_face === null)
            $this->_face = new AzureCognitiveServiceFaceController();
        return $this->_face;
    }

    public function saveToFile($filepath,$content,$overwrite = false): ?bool
    {
        if(Storage::exists($filepath) and $overwrite === false) {
            report(new \Exception('File '.$filepath.' already exist. Enable overwrite to overwrite the file, or delete it.'));
            return null;
        }
        try {
            Storage::put($filepath, $content);
        } catch (\Throwable $e){
            report($e);
            return null;
        }

        if(Storage::exists($filepath))
            return true;
        return false;
    }
}
