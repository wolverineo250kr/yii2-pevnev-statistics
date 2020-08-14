<?php

namespace wolverineo250kr\modules\statistics\models;

use Yii;
use yii\base\Model;

class StatisticCityWidgetForm extends Model {

    public $dateStart;
    public $dateEnd;

    public function rules() {
        return [
            [
                [
                    'dateStart',
                    'dateEnd',
                ],
                'date',
                'format' => 'php:Y-m-d'
            ],
        ];
    }

    public function attributeLabels() {
        return [
            'dateStart' => 'Дата начала периода',
            'dateEnd' => 'Дата окончания периода',
        ];
    }

}
