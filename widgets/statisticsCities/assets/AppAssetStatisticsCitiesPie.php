<?php

namespace wolverineo250kr\modules\statistics\widgets\statisticsCities\assets;

use yii\web\AssetBundle;
use Yii;

class AppAssetStatisticsCitiesPie extends AssetBundle {

    public $jsOptions = [];
    public $publishOptions = [
        'forceCopy' => true,
    ];
    public $sourcePath = __DIR__;
    public $css = [
        "css/statisticsCities.css?1.00",
    ];
    public $js = [ 
        "js/statisticsCities.js?v1.09",
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
