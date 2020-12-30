<?php

use backend\modules\settlement\models\search\IssuePaySearch;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssuePaySearch */

?>

<div class="issue-pay-nav">
	<?= Html::a(
		Yii::t('settlement', 'Not payed'),
		['index', 'status' => IssuePaySearch::PAY_STATUS_NOT_PAYED],
		[
			'class' => 'btn btn-warning' . ($model->payStatus === IssuePaySearch::PAY_STATUS_NOT_PAYED ? ' active btn-lg' : ''),
		]
	) ?>
	<?= Html::a(
		Yii::t('settlement', 'Payed'),
		['index', 'status' => IssuePaySearch::PAY_STATUS_PAYED],
		[
			'class' => 'btn btn-success' . ($model->payStatus === IssuePaySearch::PAY_STATUS_PAYED ? ' active btn-lg' : ''),

		])

	?>
	<?= Html::a(Yii::t('settlement', 'All'),
		['index', 'status' => IssuePaySearch::PAY_STATUS_ALL],
		[
			'class' => 'btn btn-info' . ($model->payStatus === IssuePaySearch::PAY_STATUS_ALL ? ' active btn-lg' : ''),
		])

	?>
	<?= Yii::$app->user->can(\common\models\user\User::PERMISSION_PAY_RECEIVED)
		? Html::a(Yii::t('settlement', 'Received pays'),
			['/pay-received/index'],
			[
				'class' => 'btn btn-info',
			])
		: ''
	?>
</div>
