<?php

use common\models\issue\search\SummonDocLinkSearch;
use yii\bootstrap\Nav;

/** @var SummonDocLinkSearch $searchModel */

?>

<div class="summon-doc-nav">
	<?= Nav::widget([
		'items' => [
			[
				'label' => Yii::t('issue', 'To Do'),
				'url' => ['to-do'],
				'active' => $searchModel->status === SummonDocLinkSearch::STATUS_TO_DO,
			],
			[
				'label' => Yii::t('issue', 'To Confirm'),
				'url' => ['to-confirm'],
				'active' => $searchModel->status === SummonDocLinkSearch::STATUS_TO_CONFIRM,
			],
			[
				'label' => Yii::t('issue', 'Confirmed'),
				'url' => ['confirmed'],
				'active' => $searchModel->status === SummonDocLinkSearch::STATUS_CONFIRMED,
			],
		],
		'options' => [
			'class' => 'nav nav-pills',
		],
	]) ?>
</div>
