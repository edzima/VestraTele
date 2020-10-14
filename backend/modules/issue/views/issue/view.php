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
		<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER)
			? Html::a(
				'Rozliczenia',
				['pay-calculation/index', 'issueId' => $model->id],
				['class' => 'btn btn-info'])
			: '' ?>
		<?= Html::a('Notatka', ['note/create', 'issueId' => $model->id], [
			'class' => 'btn btn-success',
		]) ?>

		<?= Html::a('Relacje', ['user/issue', 'id' => $model->id], [
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

	<?= IssueViewWidget::widget(['model' => $model]) ?>
	<?= IssueSummonsWidget::widget(['model' => $model]) ?>
	<?= IssuePaysWidget::widget(['models' => $model->pays, 'user' => Yii::$app->user->getIdentity()]) ?>
	<?= IssueNotesWidget::widget(['model' => $model]) ?>

</div>
