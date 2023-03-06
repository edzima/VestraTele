<?php

use common\helpers\Url;
use common\models\issue\search\SummonDocLinkSearch;
use yii\bootstrap\Nav;

/** @var SummonDocLinkSearch $searchModel */

?>

<p class="summon-doc-nav">
	<?= Nav::widget([
		'items' => [
			[
				'label' => Yii::t('issue', 'To Do'),
				'url' => ['to-do', Url::PARAM_ISSUE_PARENT_TYPE => $searchModel->issueParentTypeId],
				'active' => $searchModel->status === SummonDocLinkSearch::STATUS_TO_DO,
			],
			[
				'label' => Yii::t('issue', 'To Confirm'),
				'url' => ['to-confirm', Url::PARAM_ISSUE_PARENT_TYPE => $searchModel->issueParentTypeId],
				'active' => $searchModel->status === SummonDocLinkSearch::STATUS_TO_CONFIRM,
			],
			[
				'label' => Yii::t('issue', 'Confirmed'),
				'url' => ['confirmed', Url::PARAM_ISSUE_PARENT_TYPE => $searchModel->issueParentTypeId],
				'active' => $searchModel->status === SummonDocLinkSearch::STATUS_CONFIRMED,
			],
		],
		'options' => [
			'class' => 'nav nav-pills',
		],
	]) ?>
</p>
