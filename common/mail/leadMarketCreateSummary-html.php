<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use edzima\teryt\models\Region;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $regionsModels LeadMarket[] */
/* @var $withoutRegionsModels LeadMarket[] */
/* @var $totalCount int */

$this->title = Yii::t('lead', 'New {count} Leads on Market.', [
	'count' => $totalCount,
]);

$marketUrl = FrontendUrl::toRoute(['/lead/market/user'], true);
$this->params['primaryButtonText'] = Yii::t('lead', 'Lead Market');
$this->params['primaryButtonHref'] = $marketUrl;
?>
<div class="lead-market-create-summary">

	<?php foreach ($regionsModels as $regionId => $models): ?>
		<p><?= Html::a(
				Html::encode(Region::getNames()[$regionId]),
				FrontendUrl::toRoute(['/lead/market/user', 'regionId' => $regionId], true)
			) ?>: <strong><?= count($models) ?></strong></p>
	<?php endforeach; ?>

	<?php if (!empty($withoutRegionsModels)): ?>
		<p><?= Html::a(
				Yii::t('lead', 'Without City'),
				FrontendUrl::to(['/lead/market/user', 'withoutCity' => true], true)
			) ?>: <strong><?= count($withoutRegionsModels) ?></strong>
		</p>

	<?php endif; ?>


</div>
