<?php

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarket */
/* @var $users LeadMarketUser[] */

$lead = $model->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);

?>
<?php foreach ($users as $user): ?>
	<?= $user->user->getFullName() ?>

	<?php
	$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $user->market_id, 'user_id' => $user->user_id], true);
	$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $user->market_id, 'user_id' => $user->user_id], true);
	?>
	<?= Yii::t('lead', 'Accept') . ': ' . $acceptLink ?>
	<?= Yii::t('lead', 'Reject') . ': ' . $rejectLink ?>

<?php endforeach; ?>

<?= $leadLink ?>
