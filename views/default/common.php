<?

use kartik\date\DatePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
?>

<? Pjax::begin(['timeout' => 50000, 'id' => 'pjax-content']) ?>

<? $this->title = 'Общая посещаемость'; ?> 
<? $this->params['breadcrumbs'][] = ['label' => 'Статистика', 'url' => Url::toRoute([Yii::$app->controller->id . '/index'])]; ?>
<? $this->params['breadcrumbs'][] = ["label" => $this->title]; ?>

<?

$grid_columns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['class' => 'id'],
    ],
    [
        'attribute' => 'status_code',
        'filter' => Html::activeDropDownList($searchModel, 'status_code', yii\helpers\ArrayHelper::map(\wolverineo250kr\modules\statistics\models\Statistics::find()
                                ->groupBy('status_code')
                                ->all(), 'status_code', 'status_code'), ['class' => 'form-control', 'prompt' => 'Все']),
        'contentOptions' => ['class' => 'status_code'],
        'value' => function($model) {
            switch ($model->status_code) {
                case 200:
                    return "<b class='label label-success'>200</b>";
                    break;
                case 301:
                    return "<b class='label label-info'>301</b>";
                    break;
                case 302:
                    return "<b class='label label-primary'>302</b>";
                    break;
                case 400:
                    return "<b class='label label-warning'>400</b>";
                    break;
                case 404:
                    return "<b class='label label-danger'>404</b>";
                    break;
                case 500:
                    return "<b class='label label-danger'>500</b>";
                    break;
                default:
                    return $model->status_code;
                    break;
            }
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'elapsed_time',
        'contentOptions' => ['class' => 'elapsed_time'],
    ],
    [
        'attribute' => 'isAjax',
        'contentOptions' => ['width' => '160', 'class' => 'isAjax'],
        'filter' => Html::activeDropDownList($searchModel, 'isAjax', [1 => 'Да', 0 => 'Нет'], ['class' => 'form-control', 'prompt' => 'Все']),
        'value' => function($model) {
            return ($model->isAjax) ? "<b class='label label-success'>Да</b>" : "<b class='label label-info'>Нет</b>";
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'url',
        'contentOptions' => ['class' => 'url', 'style' => 'max-width:300px;overflow-y:scroll;white-space: normal;'],
    ],
    [
        'attribute' => 'url_referrer',
        'contentOptions' => ['class' => 'url_referrer', 'style' => 'max-width:300px;overflow-y:scroll;white-space: normal;'],
    ],
    [
        'attribute' => 'ip',
        'contentOptions' => ['class' => 'ip'],
    ],
    [
        'attribute' => 'sypexGeo',
        'value' => function($model) {

            return $model->getSypexObject();
        },
        'format' => 'raw',
        'contentOptions' => ['class' => 'sypexGeo'],
    ],
    [
        'attribute' => 'user_agent',
        'value' => function($model) {
            if (strpos($model->user_agent, "Firefox") !== false)
                $browser = "Firefox";
            elseif (strpos($model->user_agent, "Opera") !== false)
                $browser = "Opera";
            elseif (strpos($model->user_agent, "Chrome") !== false)
                $browser = "Chrome";
            elseif (strpos($model->user_agent, "MSIE") !== false)
                $browser = "Internet Explorer";
            elseif (strpos($model->user_agent, "Safari") !== false)
                $browser = "Safari";
            else
                $browser = "Неизвестный";
            return $browser;
        },
        'contentOptions' => ['class' => 'user_agent'],
    ],
    [
        'attribute' => 'timestamp',
        'contentOptions' => ['class' => 'incoming_timestamp'],
        'filter' => DatePicker::widget(['model' => $searchModel, 'attribute' => 'timestamp', 'language' => 'ru', 'pluginOptions' => ['format' => 'yyyy-mm-dd']]),
    ],
];
?>

<?

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'showFooter' => false,
    'summaryOptions' => ['class' => 'pull-right summary'],
    'caption' => $this->title,
    'captionOptions' => ['class' => ''],
    'options' => ['data-pjax' => 1, 'class' => 'grid-view', 'id' => 'grid-view', 'style' => "overflow-x: scroll;"],
    'tableOptions' => [
        'class' => 'table table-striped table-bordered',
        'data-path-after' => Url::toRoute([Yii::$app->controller->id . '/move-after']),
        'data-path-before' => Url::toRoute([Yii::$app->controller->id . '/move-before']),
    ],
    'columns' => $grid_columns,
]);
?>

<? Pjax::end() ?>
 
