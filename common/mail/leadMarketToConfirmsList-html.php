<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use frontend\helpers\Url as FrontendUrl;

/* @var $this yii\web\View */
/* @var $model LeadMarket */
/* @var $users LeadMarketUser[] */

$lead = $model->lead;
$leadLink = FrontendUrl::leadView($lead->getId(), true);

?>
<div class="lead-market-to-confirms-list-email">
	<?php foreach ($users as $user): ?>
		<p><?= $user->user->getFullName() ?></p>

		<?php
		$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $user->market_id, 'user_id' => $user->user_id], true);
		$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $user->market_id, 'user_id' => $user->user_id], true);
		?>
		<p><?= Html::a(Yii::t('lead', 'Accept'), $acceptLink) ?></p>
		<p><?= Html::a(Yii::t('lead', 'Reject'), $rejectLink) ?></p>

	<?php endforeach; ?>
	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>
</div>
