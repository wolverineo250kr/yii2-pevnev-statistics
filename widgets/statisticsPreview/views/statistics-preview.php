<?

use wolverineo250kr\modules\statistics\widgets\statisticsPreview\assets\AppAssetStatisticsPreview;
use yii\widgets\ActiveForm;

AppAssetStatisticsPreview::register($this);
?>

<div class="pevnev-statistic-widget-block"> 
    <?=
    \yii\helpers\Html::a('Общая посещаемость', '/statistics/common', [
        'class' => 'title'
    ])
    ?> 
    <div class="range-choice">                             
        <a href="#" id="StatisticsCurrentMonth" data-start="<?= date('Y-m-01') ?>" data-end="<?= date('Y-m-t') ?>"><span>текущий месяц</span></a>
        <a href="#" id="StatisticsYesterday" data-start="<?= date("Y-m-d", strtotime("-1 day")) ?>" data-end="<?= date("Y-m-d", strtotime("-1 day")) ?>"><span>вчера</span></a>
        <a href="#" id="StatisticsToday" data-start="<?= date('Y-m-d') ?>" data-end="<?= date('Y-m-d') ?>"><span>сегодня</span></a>        
    </div>
    <canvas class='canvas-chart' id="canvas-chart-order-form"></canvas>
    <?
    $form = ActiveForm::begin([
                'id' => 'statisticWidgetForm',
                'action' => '/statistics/chart-line',
                'options' => [
                    'class' => 'chart-line-vidget',
                ],
    ]);
    ?>
    <div class="hidden">
        <?=
        $form->field($statisticWidgetForm, 'dateStart')->hiddenInput()->label(false);
        ?>
        <?=
        $form->field($statisticWidgetForm, 'dateEnd')->hiddenInput()->label(false);
        ?>
    </div>
    <? ActiveForm::end(); ?>
    <span class="no-found" style="display: none;">Данные отсутствуют</span>
    <i class="fa fa-circle-o-notch fa-spin fa-3x" aria-hidden="true"></i>
</div> 