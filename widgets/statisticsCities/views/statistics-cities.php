<?

use wolverineo250kr\modules\statistics\widgets\statisticsCities\assets\AppAssetStatisticsCitiesPie;
use yii\widgets\ActiveForm;

AppAssetStatisticsCitiesPie::register($this);
?>

<div class="pevnev-statistic-cities-widget-block">
    <span class="title" style="">Посещаемость по городам</span>
    <div class="range-choice">                             
        <a href="#" id="CityStatisticsCurrentMonth" data-start="<?= date('Y-m-01') ?>" data-end="<?= date('Y-m-t') ?>"><span>текущий месяц</span></a>
        <a href="#" id="CityStatisticsYesterday" data-start="<?= date("Y-m-d", strtotime("-1 day")) ?>" data-end="<?= date("Y-m-d", strtotime("-1 day")) ?>"><span>вчера</span></a>
        <a href="#" id="CityStatisticsToday" data-start="<?= date('Y-m-d') ?>" data-end="<?= date('Y-m-d') ?>"><span>сегодня</span></a>        
    </div>
    <canvas id="citiesPie"></canvas>
    <?
    $form = ActiveForm::begin([
                'id' => 'citiesStatisticWidgetForm',
                'action' => '/statistics/cities-pie',
                'options' => [
                    'class' => 'chart-line-vidget',
                ],
    ]);
    ?>
    <div class="hidden">
        <?=
        $form->field($statisticCityWidgetForm, 'dateStart')->hiddenInput()->label(false);
        ?>
        <?=
        $form->field($statisticCityWidgetForm, 'dateEnd')->hiddenInput()->label(false);
        ?>
    </div>
    <? ActiveForm::end(); ?>
    <span class="no-found" style="display: none;">Данные отсутствуют</span>
    <i class="fa fa-circle-o-notch fa-spin fa-3x" aria-hidden="true"></i>
</div> 
