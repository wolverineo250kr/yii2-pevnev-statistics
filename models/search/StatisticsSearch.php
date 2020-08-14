<?php

namespace wolverineo250kr\modules\statistics\models\search;

use wolverineo250kr\modules\statistics\models\Statistics;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class SpheresSearch
 */
class StatisticsSearch extends Statistics {

    const COUNT = 50;

    public $id;
    public $status_code;
    public $elapsed_time;
    public $isAjax;
    public $url;
    public $url_referrer;
    public $user_agent;
    public $timestamp;

    /**
     * Правила валидации
     * @return array
     */
    public function rules() {
        return [
            [
                [
                    'id',
                    'status_code',
                    'elapsed_time',
                    'isAjax',
                    'url',
                    'url_referrer',
                    'ip',
                    'user_agent',
                    'timestamp',
                ],
                'safe'
            ],
        ];
    }

    /**
     * Сценарии
     * @return array
     */
    public function scenarios() {
        return Model::scenarios();
    }

    /**
     * Названия дополнительных полей
     * поиска документов
     * @return array
     */
    public function attributeLabels() {
        $label = parent::attributeLabels();

        return $label;
    }

    /**
     * Создает DataProvider на основе переданных данных
     * @param $params - параметры
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Statistics::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this::COUNT,
            ],
            'sort' => array(
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'status_code',
                    'elapsed_time',
                    'isAjax',
                    'url',
                    'url_referrer',
                    'ip',
                    'user_agent',
                    'timestamp',
                ],
            ),
        ]);

        $this->load($params);


        // Если валидация не пройдена, то ничего не выводить
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Фильтрация
        if (mb_strlen($this->id)) {
            $query->andWhere(['id' => $this->id,]);
        }
        if (mb_strlen($this->status_code)) {
            $query->andWhere(['status_code' => $this->status_code]);
        }

        if (mb_strlen($this->elapsed_time)) {
            $query->andFilterWhere(['like', 'elapsed_time', $this->elapsed_time]);
        }
        if (mb_strlen($this->isAjax)) {
            $query->andFilterWhere(['like', 'isAjax', $this->isAjax]);
        }
        if (mb_strlen($this->url)) {
            $query->andWhere(['like', 'url', $this->url,]);
        }
        if (mb_strlen($this->url_referrer)) {
            $query->andWhere(['like', 'url_referrer', $this->url_referrer,]);
        }
        if (mb_strlen($this->ip)) {
            $query->andWhere(['ip' => $this->ip,]);
        }
        if (mb_strlen($this->timestamp)) {
            $query->andWhere(['timestamp' => $this->timestamp,]);
        }
        if (mb_strlen($this->timestamp)) {
            $query->andFilterWhere(['<=', parent::tableName() . '.timestamp', $this->timestamp . ' 23:59:59']);
            $query->andFilterWhere(['>=', parent::tableName() . '.timestamp', $this->timestamp . ' 00:00:00']);
        }
        return $dataProvider;
    }

}
