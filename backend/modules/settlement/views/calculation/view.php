<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Url;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\modules\issue\widgets\IssuePaysWidget;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssuePayCalculation */

$this->title = Yii::t('settlement', 'Settlement {type}', ['type' => $model->getTypeName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue, 'url' => ['issue', 'id' => $model->issue_id]];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);
?>
<div class="issue-pay-calculation-view">

	<p>
		<?= Yii::$app->user->can(User::PERMISSION_CALCULATION)
			? Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: '' ?>


		<?= !$model->isPayed() && Yii::$app->user->can(User::PERMISSION_PAY)
			? Html::a(Yii::t('backend', 'Generate pays'), ['pays', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: '' ?>


		<?= !$model->isPayed() && Yii::$app->user->can(User::PERMISSION_CALCULATION)
			? Html::a(Yii::t('backend', 'Set problem status'), ['problem-status', 'id' => $model->id], ['class' => 'btn btn-warning'])
			: '' ?>



		<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) && $model->hasPays()
			? Html::a(Yii::t('backend', 'Provisions'), ['/provision/settlement/set', 'id' => $model->id], ['class' => 'btn btn-success'])
			: '' ?>


		<?php //@todo enable this after refactoring note
		//  Html::a('Notatka', ['note/create', 'issueId' => $model->issue_id, 'type' => IssueNote::TYPE_PAY], ['class' => 'btn btn-success',]) ?>


		<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) ? Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) : '' ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'issue',
				'format' => 'raw',
				'value' => Html::a($model->issue, Url::issueView($model->issue_id), ['target' => '_blank']),
				'label' => 'Sprawa',
			],
			'providerName',
			'owner',
			[
				'attribute' => 'problemStatusName',
				'visible' => $model->hasProblemStatus(),
			],
			'value:currency',
			[
				'attribute' => 'valueToPay',
				'format' => 'currency',
				'visible' => !$model->isPayed(),
			],
			[
				'attribute' => 'payment_at',
				'format' => 'date',
				'visible' => $model->isPayed(),
			],

			[
				'attribute' => 'details',
				'format' => 'ntext',
				'visible' => !empty($model->details),
			],
		],
	]) ?>

	<?= IssuePaysWidget::widget([
		'models' => $model->pays,
		'editPayBtn' => Yii::$app->user->can(User::ROLE_BOOKKEEPER),
	]) ?>

	<?php
	//@todo enable this after refactoring note
	/*IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlyPays()->all(),
		'type' => IssueNotesWidget::TYPE_PAY,

	])
	*/
	?>


</div>
