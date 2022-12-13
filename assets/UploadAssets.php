<?php

namespace app\assets;

use yii\web\AssetBundle;

class UploadAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        '/js/convert_to_mp3.js'
    ];
    public $depends = [
        'app\assets\MusicAsset'
    ];
}

?>