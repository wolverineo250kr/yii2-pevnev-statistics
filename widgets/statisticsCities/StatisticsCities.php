<?

namespace wolverineo250kr\modules\statistics\widgets\statisticsCities;

use yii\base\Widget;
use wolverineo250kr\modules\statistics\models\StatisticCityWidgetForm;

class StatisticsCities extends Widget {

    public function run() {
        return $this->render('statistics-cities', [
                    'statisticCityWidgetForm' => new StatisticCityWidgetForm(),
        ]);
    }

}
