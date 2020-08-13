<?php

use backend\helpers\Url;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssuePaysWidget;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssuePayCalculation */

$this->title = 'Rozliczenie ' . $model->getTypeName();

$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue, 'url' => Url::issueView($model->issue_id)];
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue, 'url' => ['index', 'issueId' => $model->issue_id]];

$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$isMainBookeeper = Yii::$app->user->can(User::ROLE_BOOKKEEPER);
?>
<div class="issue-pay-calculation-view">

	<h1>Rozliczenie
	</h1>

	<p>
		<?= $isMainBookeeper ? Html::a('Edycja', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>

		<?= $isMainBookeeper ? Html::a('Prowizje', ['/provision/calculation/set', 'id' => $model->id], ['class' => 'btn btn-warning']) : '' ?>


		<?= Html::a('Notatka', ['note/create', 'issueId' => $model->issue_id, 'type' => IssueNote::TYPE_PAY], [
			'class' => 'btn btn-success',
		]) ?>

		<?= $isMainBookeeper ? Html::a('Usuń', ['delete', 'id' => $model->id], [
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
			'typeName',
			[
				'attribute' => 'issue',
				'format' => 'raw',
				'value' => Html::a($model->issue, Url::issueView($model->issue_id), ['target' => '_blank']),
				'label' => 'Sprawa',
			],
			'value:currency',
			'providerName',
			'created_at:date',
			'updated_at:date',
			'payment_at:date',
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

	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlyPays()->all(),
		'type' => IssueNotesWidget::TYPE_PAY,
	]) ?>


</div>
