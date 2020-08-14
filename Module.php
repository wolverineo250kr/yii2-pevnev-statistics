<?php

namespace wolverineo250kr\statistics;

use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'wolverineo250kr\statistics\controllers';
    public $defaultRoute        = 'default';

    public function bootstrap($app)
    {

    }

public function init()
    {
        parent::init();
    }
}