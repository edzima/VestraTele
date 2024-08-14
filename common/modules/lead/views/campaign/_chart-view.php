<?php

use common\modules\lead\models\searches\LeadCampaignCostSearch;
use common\widgets\charts\ChartsWidget;

/* @var $this yii\web\View */

/* @var $campaignCost LeadCampaignCostSearch */

?>

<div class="view-charts">
	<?= ChartsWidget::widget([
		'type' => ChartsWidget::TYPE_BAR,
		'series' => $campaignCost->getCostData(),
	]) ?>
</div>
