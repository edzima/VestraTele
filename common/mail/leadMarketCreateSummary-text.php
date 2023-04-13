<?php

use common\modules\lead\models\LeadMarket;
use edzima\teryt\models\Region;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $regionsModels LeadMarket[] */
/* @var $withoutRegionsModels LeadMarket[] */
/* @var $totalCount int */

$marketUrl = FrontendUrl::toRoute(['/lead/market/user'], true);

?>
<?= Yii::t('lead', 'New {count} Leads on Market.', [
	'count' => $totalCount,
]) ?>

<?php foreach ($regionsModels as $regionId => $models): ?>
	<?=
	Region::getNames()[$regionId] . ': '
	. count($models)
	. ' '
	. FrontendUrl::toRoute(['/lead/market/user', 'regionId' => $regionId], true)
	?>
<?php endforeach; ?>

<?= !empty($withoutRegionsModels)
	? (Yii::t('lead', 'Without City')
		. ': '
		. count($withoutRegionsModels)
		. ' '
		. FrontendUrl::to(['/lead/market/user', 'withoutAddress' => true], true)
	)
	: ''
?>

<?= $marketUrl ?>

