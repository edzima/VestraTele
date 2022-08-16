<?php

use common\modules\lead\models\LeadMarket;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $regionsModels LeadMarket[] */
/* @var $withoutRegionsModels LeadMarket[] */
/* @var $totalCount int */

$marketUrl = FrontendUrl::toRoute(['/lead/market/index'], true);

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

