<?php

namespace wolverineo250kr\modules\statistics\models;

use common\extensions\sypexgeo\Sypexgeo;
use yii\db\ActiveRecord;

class Statistics extends ActiveRecord {

    const AJAX_TRUE = 1;
    const AJAX_FALSE = 0;

    public $sypexGeo;

    public static function tableName() {
        return '{{%statistics}}';
    }

    public function rules() {
        return [
            [['id', 'status_code', 'isAjax', 'ip'], 'integer'],
            [['isAjax'], 'default', 'value' => self::AJAX_FALSE],
            [['isAjax'], 'in', 'range' => [self::AJAX_TRUE, self::AJAX_FALSE]],
            [
                ['timestamp'],
                'date',
                'format' => 'php:Y-m-d H:i:s'
            ],
            [['session_id'], 'string', 'max' => 255],
            [['url', 'url_referrer', 'user_agent'], 'string'],
            [['elapsed_time'], 'number'],
        ];
    }

    public function getSypexObject() {
        $SxGeo = new Sypexgeo();
        $ip = long2ip(sprintf("%d", $this->ip));
        $geo = $SxGeo->get($ip);
        if ($geo) {
            return 'Страна:<b>' . $geo['country']["name_ru"].'</b><br/>Город:<b>' . $geo['city']["name_ru"].'</b>';
        }
        return '';
    }

    public function attributeLabels() {
        return [
            'id' => 'id',
            'session_id' => 'Уникальный идентификатор посетителя',
            'isAjax' => 'Асинхронный запрос',
            'status_code' => 'Код ответа',
            'elapsed_time' => 'Время выполнения запроса',
            'url' => 'Адресс станицы',
            'url_referrer' => 'Адресс рефер станицы',
            'ip' => 'IP адресс',
            'user_agent' => 'Юзер агент',
            'timestamp' => 'Дата и время посещения'
        ];
    }

}
