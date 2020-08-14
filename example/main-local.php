<?php
$config = [
 'on afterAction' => function () {
    $pevnevStatistics = new \wolverineo250kr\modules\statistics\components\StatisticsComponent();
   $pevnevStatistics->hitIt();
},
];


return $config;
