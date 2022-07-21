<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;

/* @var $this yii\web\View */
/* @var $regionsModels LeadMarket[] */
/* @var $withoutRegionsModels LeadMarket[] */
/* @var $totalCount int */

$marketUrl = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market/index']);

?>
<div class="lead-market-create-summary">
	<p><?= Yii::t('lead', 'New {count} Leads on Market.', [
			'count' => $totalCount,
		]) ?>
	</p>

	<?php foreach ($regionsModels as $regionName => $models): ?>
		<p><?= Html::encode($regionName) ?>: <strong><?= count($models) ?></strong></p>
	<?php endforeach; ?>

	<?php if (!empty($withoutRegionsModels)): ?>
		<p><?= Yii::t('lead', 'Others') ?>:
			<strong><?= count($withoutRegionsModels) ?></strong>
		</p>

	<?php endif; ?>

	<p><?= Html::a(Yii::t('lead', 'Lead Market'), $marketUrl) ?></p>

</div>
