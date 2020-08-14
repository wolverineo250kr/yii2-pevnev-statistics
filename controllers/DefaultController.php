<?php

namespace wolverineo250kr\modules\statistics\controllers;

use wolverineo250kr\modules\statistics\models\StatisticCityWidgetForm;
use common\extensions\sypexgeo\Sypexgeo;
use wolverineo250kr\modules\statistics\models\form\StatisticsForm;
use wolverineo250kr\modules\statistics\models\search\StatisticsSearch;
use wolverineo250kr\modules\statistics\models\Statistics;
use wolverineo250kr\modules\statistics\models\StatisticWidgetForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 *
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'common',
                            'chart-line',
                            'cities-pie',
                            'index',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => false,
            ],
        ];
    }

    public function beforeAction($action)
    {

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCommon()
    {
        $cookiePanel = (isset(Yii::$app->request->cookies["panel-action"])) ? Yii::$app->request->cookies["panel-action"]->value : NULL;
        $searchModel = new StatisticsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $modelForm = new StatisticsForm();

        return $this->render('common', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelForm' => $modelForm,
            'cookiePanel' => $cookiePanel,
        ]);
    }

    public function actionChartLineValidate()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }
        $model = new \wolverineo250kr\modules\statistics\models\StatisticWidgetForm();
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionChartLine()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $statisticsForm = Statistics::find();

            /*
             * item ['x'=>value,'y'=>value]
             */
            $data = [
                'all' => [],
                'allOfAll' => [],
            ];

            $countData = [
                'all' => 0,
                'allOfAll' => 0,
            ];

            /*
             * value -> all data x`s value
             */
            $labels = [];

            $dateStart = date('Y-m-d');
            $dateEnd = date('Y-m-d');

            $statisticWidgetForm = new \wolverineo250kr\modules\statistics\models\StatisticWidgetForm();

            if ($statisticWidgetForm->load(Yii::$app->request->post())) {
                if ($statisticWidgetForm->validate()) {
                    $dateStart = $statisticWidgetForm->dateStart;
                    $dateEnd = $statisticWidgetForm->dateEnd;
                }
            }

            $timestampStart = $dateStart . ' 00:00:00';
            $timestampEnd = $dateEnd . ' 23:59:59';

            // Уники
            $queryTmp = new \yii\db\Query();
            $queryTmp->select(['id', 'timestamp'])
                ->from(Statistics::tableName())
                ->andFilterWhere(['>=', 'timestamp', $timestampStart])
                ->andFilterWhere(['<=', 'timestamp', $timestampEnd])
                ->andWhere(['isAjax' => 0])
                ->andWhere(['status_code' => 200])
                ->groupBy(['session_id']);

            $queryErrorUniq = new \yii\db\Query();
            $queryErrorUniq->select(new \yii\db\Expression('UNIX_TIMESTAMP(timestamp) as date,count(id) as count, (time_to_sec(`timestamp`)- time_to_sec(`timestamp`)%(30*60))  AS half_hour, (UNIX_TIMESTAMP(DATE_FORMAT(timestamp,"%Y-%m-%d"))) as date_log'))
                ->from(['tmp' => $queryTmp])
                ->orderBy(['timestamp' => SORT_ASC])
                ->groupBy('half_hour,date_log');

            foreach ($queryErrorUniq->each() as $statisticsFormLog) {
                $time = (int)$statisticsFormLog['half_hour'];
                $date = 1000 * (int)$statisticsFormLog['date'];
                $data['all'][] = ['x' => $date, 'y' => (int)$statisticsFormLog['count']];
                $labels[] = $date;

                $countData['all'] += (int)$statisticsFormLog['count'];
            }

            // Все посещеия
            $statisticsFormAllFromAll = Statistics::find();
            $statisticsFormAllFromAll->select(new \yii\db\Expression('UNIX_TIMESTAMP(timestamp) as date,count(id) as count, (time_to_sec(`timestamp`)- time_to_sec(`timestamp`)%(30*60))  AS half_hour,(UNIX_TIMESTAMP(DATE_FORMAT(timestamp,"%Y-%m-%d"))) as date_log'));
            $statisticsFormAllFromAll->orderBy(['timestamp' => SORT_ASC]);
            $statisticsFormAllFromAll->asArray();
            $statisticsFormAllFromAll->groupBy(new \yii\db\Expression('half_hour,date_log'));
            $statisticsFormAllFromAll->where(['isAjax' => 0]);
            $statisticsFormAllFromAll->andWhere(['status_code' => 200]);
            $statisticsFormAllFromAll->andFilterWhere(['>=', 'timestamp', $timestampStart]);
            $statisticsFormAllFromAll->andFilterWhere(['<=', 'timestamp', $timestampEnd]);
            $statisticsFormEachFromAll = $statisticsFormAllFromAll->all();

            foreach ($statisticsFormEachFromAll as $statisticsFormLog) {
                $time = (int)$statisticsFormLog['half_hour'];
                $date = 1000 * (int)$statisticsFormLog['date'];
                $data['allOfAll'][] = ['x' => $date, 'y' => (int)$statisticsFormLog['count']];
                $labels[] = $date;

                $countData['allOfAll'] += (int)$statisticsFormLog['count'];
            }

            /*
             * а вот тут сюрприз нужен подзапрос
             */

            $queryTmp = new \yii\db\Query();
            $queryTmp->select(['id', 'timestamp'])
                ->from(Statistics::tableName())
                ->andFilterWhere(['>=', 'timestamp', $timestampStart])
                ->andFilterWhere(['<=', 'timestamp', $timestampEnd])
                ->andWhere(['isAjax' => 0])
                ->andWhere(['status_code' => 200])
                ->groupBy(['session_id']);

            $queryErrorUniq = new \yii\db\Query();
            $queryErrorUniq->select(new \yii\db\Expression('UNIX_TIMESTAMP(timestamp) as date,count(id) as count, (time_to_sec(`timestamp`)- time_to_sec(`timestamp`)%(30*60))  AS half_hour, (UNIX_TIMESTAMP(DATE_FORMAT(timestamp,"%Y-%m-%d"))) as date_log'))
                ->from(['tmp' => $queryTmp])
                ->orderBy(['timestamp' => SORT_ASC])
                ->groupBy('half_hour,date_log');
            $countData['errorUniq'] = 0;
            foreach ($queryErrorUniq->each() as $statisticsFormLog) {
                $time = (int)$statisticsFormLog['half_hour'];
                $date = 1000 * (int)$statisticsFormLog['date'];
                $data['errorUniq'][] = ['x' => $date, 'y' => (int)$statisticsFormLog['count']];
                $countData['errorUniq'] += (int)$statisticsFormLog['count'];

                if (!in_array($date, $labels)) {
                    $labels[] = $date;
                }
            }

            return [
                'labels' => $labels,
                'all' => $data['all'],
                'allOfAll' => $data['allOfAll'],
                'countData' => $countData
            ];
        }

        return $this->render('chart-line');
    }

    public function actionCitiesPie()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $statisticsForm = Statistics::find();

        $statisticCityWidgetForm = new StatisticCityWidgetForm();

        $dateStart = date('Y-m-d');
        $dateEnd = date('Y-m-d');

        if ($statisticCityWidgetForm->load(Yii::$app->request->post())) {
            if ($statisticCityWidgetForm->validate()) {
                $dateStart = $statisticCityWidgetForm->dateStart;
                $dateEnd = $statisticCityWidgetForm->dateEnd;
            }
        }

        $timestampStart = $dateStart . ' 00:00:00';
        $timestampEnd = $dateEnd . ' 23:59:59';

        $statisticsForm->select(['ip', 'COUNT(ip) AS visits']);
        $statisticsForm->groupBy(['ip']);
        $statisticsForm->having('COUNT(ip) > 1');
        $statisticsForm->orderBy(['count(ip)' => SORT_DESC]);
        $statisticsForm->where(['isAjax' => 0]);
        $statisticsForm->andWhere(['status_code' => 200]);
        $statisticsForm->andFilterWhere(['>=', 'timestamp', $timestampStart]);
        $statisticsForm->andFilterWhere(['<=', 'timestamp', $timestampEnd]);

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($statisticsForm->createCommand()->getRawSql());
        $results = $command->queryAll();

        if (!$results) {
            return [];
        }

        $arrayGeoPowered = [];
        $SxGeo = new Sypexgeo();
        foreach ($results as $ndex => $result) {

            $arrayGeoPowered[$ndex]['ip'] = $result['ip'];
            $arrayGeoPowered[$ndex]['visits'] = $result['visits'];
            $arrayGeoPowered[$ndex]['geo'] = $SxGeo->get($result['ip']);
        }

        $preFinalArray = [];
        foreach ($arrayGeoPowered as $index => $geoPowered) {
            if ($geoPowered['geo']['city']['id']) {
                $preFinalArray[$index]['cityId'] = $geoPowered['geo']['city']['id'];
                $preFinalArray[$index]['cityName'] = $geoPowered['geo']['city']['name_ru'];
                $preFinalArray[$index]['visits'] = (int)$geoPowered['visits'];
            }
        }

        $finalArray = [];
        foreach ($preFinalArray as $row) {
            $cityId = $row['cityId'];
            if (!isset($finalArray[$cityId])) {
                $finalArray[$cityId]['cityName'] = $row['cityName'];
                $finalArray[$cityId]['visits'] = 0;
            }
            $finalArray[$cityId]['visits'] += $row['visits'];
        }

        $array_name = [];

        foreach ($finalArray as $key => $row) {
            $array_name[$key]['visits'] = $row['visits'];
        }
        array_multisort($array_name, SORT_DESC, $finalArray);

        array_values($finalArray);

        $theRest = [];
        $theRest['visits'] = 0;
        foreach ($finalArray as $key => $row) {
            if ($key > 15) {
                $theRest['cityName'] = 'Остальные';
                $theRest['visits'] += $row['visits'];
                unset($finalArray[$key]);
            }
        }

        $finalArray[16] = $theRest;
        return array_values($finalArray);
    }

}