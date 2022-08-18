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
$marketLink = FrontendUrl::toRoute(['/lead/market/view', 'id' => $model->id], true);
?>
<div class="lead-market-to-confirms-list-email">

	<?= Yii::t('lead', 'Status Market') ?>: <?= Html::encode($model->getStatusName()) ?>
	<?= Html::a(Html::encode($marketLink), $marketLink) ?>

	<?php foreach ($users as $user): ?>
		<p><?= $user->user->getFullName() ?></p>

		<?php
		$acceptLink = FrontendUrl::toRoute(['/lead/market-user/accept', 'market_id' => $user->market_id, 'user_id' => $user->user_id], true);
		$rejectLink = FrontendUrl::toRoute(['/lead/market-user/reject', 'market_id' => $user->market_id, 'user_id' => $user->user_id], true);
		?>
		<p><?= Html::a(Yii::t('lead', 'Accept'), $acceptLink) ?></p>
		<p><?= Html::a(Yii::t('lead', 'Reject'), $rejectLink) ?></p>

	<?php endforeach; ?>

	<p><?= Yii::t('lead', 'Status Lead') ?>: <?= Html::encode($lead->getStatusName()) ?></p>

	<p><?= Yii::t('lead', 'Type Lead') ?>: <?= Html::encode($lead->getTypeName()) ?></p>

	<p><?= Html::a(Html::encode($leadLink), $leadLink) ?></p>
</div>
