<?php

use common\models\issue\Issue;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssuePaysWidget;
use common\modules\issue\widgets\IssueSummonsWidget;
use common\modules\issue\widgets\IssueViewWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
$this->title = $model->longId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer, 'url' => ['/user/customer/view', 'id' => $model->customer->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-view">
	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Yii::$app->user->can(User::PERMISSION_NOTE) ? Html::a(Yii::t('backend', 'Create note'), ['note/create', 'issueId' => $model->id], [
			'class' => 'btn btn-success',
		]) : '' ?>

		<?= Html::a(Yii::t('common', 'Issue users'), ['user/issue', 'id' => $model->id], [
			'class' => 'btn btn-success',
		]) ?>

		<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) ? Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'confirm' => 'Czy napewno chcesz usunąć?',
				'method' => 'post',
			],
		]) : '' ?>

	</p>
	<p>
		<?= Yii::$app->user->can(User::PERMISSION_CALCULATION)
			? Html::a(
				Yii::t('backend', 'Calculations'),
				['/settlement/calculation/issue', 'id' => $model->id],
				['class' => 'btn btn-info'])
			: '' ?>

		<?= Yii::$app->user->can(User::PERMISSION_COST)
			? Html::a(
				Yii::t('backend', 'Costs'),
				['/settlement/cost/issue', 'id' => $model->id],
				['class' => 'btn btn-info'])
			: '' ?>
	</p>

	<?= IssueViewWidget::widget(['model' => $model]) ?>
	<?= IssueSummonsWidget::widget(['model' => $model]) ?>
	<?= IssuePaysWidget::widget(['models' => $model->pays, 'user' => Yii::$app->user->getIdentity()]) ?>
	<?= IssueNotesWidget::widget(['model' => $model]) ?>

</div>
