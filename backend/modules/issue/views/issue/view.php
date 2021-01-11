<?php

use backend\modules\issue\widgets\SummonGrid;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\models\issue\Issue;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssueViewWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $calculationsDataProvider DataProviderInterface */
/* @var $summonDataProvider DataProviderInterface */

$this->title = $model->longId;

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer, 'url' => ['/user/customer/view', 'id' => $model->customer->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-view">
	<p>

		<?= Yii::$app->user->can(User::PERMISSION_NOTE) ? Html::a(Yii::t('backend', 'Create note'), ['note/create', 'issueId' => $model->id], [
			'class' => 'btn btn-info',
		]) : '' ?>

		<?= Yii::$app->user->can(User::PERMISSION_SUMMON) ? Html::a(Yii::t('backend', 'Create summon'), ['summon/create', 'issueId' => $model->id], [
			'class' => 'btn btn-warning',
		]) : '' ?>

		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>


		<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) ? Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'confirm' => 'Czy napewno chcesz usunąć?',
				'method' => 'post',
			],
		]) : '' ?>


	</p>
	<p>
		<?= Yii::$app->user->can(User::PERMISSION_CALCULATION_TO_CREATE)
			? Html::a(
				Yii::t('backend', 'Create settlement'),
				['/settlement/calculation/create', 'id' => $model->id],
				['class' => 'btn btn-success'])
			: '' ?>

		<?= Yii::$app->user->can(User::PERMISSION_COST)
			? Html::a(
				Yii::t('backend', 'Costs'),
				['/settlement/cost/issue', 'id' => $model->id],
				['class' => 'btn btn-warning'])
			: '' ?>
	</p>


	<?= $calculationsDataProvider->getTotalCount() > 0
		? IssuePayCalculationGrid::widget([
			'dataProvider' => $calculationsDataProvider,
			'caption' => Yii::t('settlement', 'Settlements'),
			'withIssue' => false,
			'summary' => '',
			'withIssueType' => false,
			'withCustomer' => false,
			'withDates' => false,
		])
		: ''
	?>

	<?= IssueViewWidget::widget(['model' => $model]) ?>

	<?= $summonDataProvider->getTotalCount() > 0
		? SummonGrid::widget([
			'dataProvider' => $summonDataProvider,
			'summary' => '',
			'withCaption' => true,
			'withIssue' => false,
			'withCustomer' => false,
			'withOwner' => false,
			'withContractor' => true,
			'withUpdatedAt' => false,
		])
		: ''
	?>

	<?= IssueNotesWidget::widget(['model' => $model]) ?>

</div>
