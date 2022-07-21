<?php

use common\modules\lead\models\LeadMarket;

/* @var $this yii\web\View */
/* @var $regionsModels LeadMarket[] */
/* @var $withoutRegionsModels LeadMarket[] */
/* @var $totalCount int */

$marketUrl = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['lead/market/index']);

?>
<?= Yii::t('lead', 'New {count} Leads on Market.', [
	'count' => $totalCount,
]) ?>

<?php foreach ($regionsModels as $regionName => $models): ?>
	<?= $regionName . ': ' . count($models) ?>
<?php endforeach; ?>

<?= !empty($withoutRegionsModels)
	? Yii::t('lead', 'Others') . ': ' . count($withoutRegionsModels)
	: ''
?>

<?= $marketUrl ?>

