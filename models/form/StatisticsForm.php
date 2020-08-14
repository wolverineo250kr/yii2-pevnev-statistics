<?php

namespace wolverineo250kr\modules\statistics\models\form;

use Yii;
use yii\base\Model;
use wolverineo250kr\modules\statistics\models\Statistics;

class StatisticsForm extends Statistics {

 

    public function rules() {
        $parentRules = parent::rules();
        return $parentRules;
    }

    public function attributeLabels() {
        $parentlabels = parent::attributeLabels();
        return $parentlabels;
    }

}
