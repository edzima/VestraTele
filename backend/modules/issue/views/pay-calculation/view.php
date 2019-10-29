<?php

use backend\helpers\Url;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssuePaysWidget;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssuePayCalculation */

$this->title = 'Rozliczenie: ' . $model->issue;

$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue, 'url' => Url::issueView($model->issue_id)];
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-pay-calculation-view">

	<h1>Rozliczenie: <?= Html::a(
			$model->issue,
			Url::issueView($model->issue_id),
			['target' => '_blank']) ?>
	</h1>

	<p>
		<?= Html::a('Edycja', ['update', 'id' => $model->issue_id], ['class' => 'btn btn-primary']) ?>

		<?= Html::a('Notatka', ['note/create', 'issueId' => $model->issue_id, 'type' => IssueNote::TYPE_PAY], [
			'class' => 'btn btn-success',
		]) ?>

		<?= Html::a('Usuń', ['delete', 'id' => $model->issue_id], [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'statusName',
			'value',
			'payName',
			[
				'attribute' => 'payCityDetails',
				'format' => 'raw',
				'value' => static function (IssuePayCalculation $model) {
					if ($model->issue->pay_city_id !== null) {
						return Html::a(
							Html::encode($model->issue->payCity->city->name),
							Url::payCityDetails($model->issue->pay_city_id),
							['target' => '_blank']);
					}
				},
			],

			'created_at:date',
			'updated_at:date',
			[
				'attribute' => 'details',
				'format' => 'ntext',
				'visible' => !empty($model->details),
			],
		],
	]) ?>

	<?= IssuePaysWidget::widget(['models' => $model->issue->pays]) ?>

	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlyPays()->all(),
		'type' => IssueNotesWidget::TYPE_PAY,
	]) ?>


</div>
