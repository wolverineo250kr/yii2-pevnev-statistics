<?

namespace wolverineo250kr\modules\statistics\widgets\statisticsPreview;

use wolverineo250kr\modules\statistics\models\StatisticWidgetForm;
use yii\base\Widget;

class StatisticsPreview extends Widget {

    public function run() {
        return $this->render('statistics-preview', [
                    'statisticWidgetForm' => new StatisticWidgetForm(),
        ]);
    }

}
