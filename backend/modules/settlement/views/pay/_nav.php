<?php

use backend\modules\settlement\models\search\IssuePaySearch;
use common\models\user\Worker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssuePaySearch */

?>

<div class="issue-pay-nav">
	<?= Html::a(
		Yii::t('settlement', 'Not payed'),
		Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
			? ['index', 'status' => IssuePaySearch::PAY_STATUS_NOT_PAYED]
			: ['delayed']
		,
		[
			'class' => 'btn btn-warning' . ($model->payStatus === IssuePaySearch::PAY_STATUS_NOT_PAYED ? ' active btn-lg' : ''),
		]
	) ?>
	<?= Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
		? Html::a(
			Yii::t('settlement', 'Payed'),
			['index', 'status' => IssuePaySearch::PAY_STATUS_PAYED],
			[
				'class' => 'btn btn-success' . ($model->payStatus === IssuePaySearch::PAY_STATUS_PAYED ? ' active btn-lg' : ''),

			])
		: ''
	?>
	<?= Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
		? Html::a(Yii::t('settlement', 'All'),
			['index', 'status' => IssuePaySearch::PAY_STATUS_ALL],
			[
				'class' => 'btn btn-info' . ($model->payStatus === IssuePaySearch::PAY_STATUS_ALL ? ' active btn-lg' : ''),
			])
		: ''
	?>
</div>
