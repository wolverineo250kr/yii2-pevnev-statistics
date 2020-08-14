<?php

namespace wolverineo250kr\modules\statistics\widgets\statisticsPreview\assets;

use yii\web\AssetBundle;
use Yii;

class AppAssetStatisticsPreview extends AssetBundle {

    public $jsOptions = [];
    public $publishOptions = [
        'forceCopy' => true,
    ];
    public $sourcePath = __DIR__;
    public $css = [
        "css/statisticsPreview.css?1.00",
    ];
    public $js = [
        'js/moment.js',
        "js/Chart.js?v1.00",
        "js/utils.js?v1.00",
        "js/statisticsPreview.js?v1.01",
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
