<?php

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\modules\court\models\Lawsuit;
use common\modules\court\widgets\LawsuitSmsBtnWidget;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\IssueColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var Lawsuit $model */
$this->title = $model->getName();
if ($model->is_appeal) {
	$this->title .= ' - ' . Yii::t('court', 'Is Appeal');
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="court-lawsuit-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('court', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('court', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('court', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-4">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'attribute' => 'courtName',
						'value' => Html::a($model->court->name, ['court/view', 'id' => $model->court_id]),
						'format' => 'html',
					],
					[
						'attribute' => 'locationName',
						'visible' => !empty($model->location),
					],
					[
						'attribute' => 'url',
						'visible' => !empty($model->url),
						'format' => 'url',
					],
					[
						'attribute' => 'presenceOfTheClaimantName',
					],
					[
						'attribute' => 'signature_act',
						'visible' => !empty($model->signature_act),
					],
					[
						'attribute' => 'room',
						'visible' => !empty($model->room),
					],
					[
						'attribute' => 'due_at',
						'format' => 'datetime',
						'visible' => !empty($model->due_at),
					],
					[
						'attribute' => 'details',
						'visible' => !empty($model->details),
					],
					'creator',
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>

		</div>

		<div class="col-md-6 col-lg-6">

			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => $model->getIssues()
						->with('customer')
						->with('entityResponsible'),
				]),
				'summary' => false,
				'columns' => [
					[
						'class' => IssueColumn::class,
						'viewBaseUrl' => '/issue/issue/view',
					],
					[
						'class' => CustomerDataColumn::class,
						'value' => 'customer.fullName',
					],
					[
						'class' => IssueTypeColumn::class,
						'contentBold' => false,
						'valueType' => IssueTypeColumn::VALUE_NAME,

					],
					[
						'class' => IssueStageColumn::class,
						'valueType' => IssueStageColumn::VALUE_NAME,
					],
					[
						'attribute' => 'entityResponsible',
					],
					[
						'value' => function (IssueInterface $issue) use ($model): string {
							return LawsuitSmsBtnWidget::widget([
								'issue' => $issue,
								'model' => $model,
							]);
						},
						'format' => 'raw',
					],
					[
						'value' => function (IssueInterface $issue) use ($model): string {
							return Html::a(
								Html::icon('remove'),
								['unlink-issue', 'id' => $model->id, 'issueId' => $issue->getIssueId()], [
								'title' => Yii::t('court', 'Delete'),
								'aria-label' => Yii::t('court', 'Delete'),
								'data-method' => 'POST',
								'data-confirm' => Yii::t('court', 'Are you sure you want to delete this Issue from Lawsuit?'),
							]);
						},
						'format' => 'raw',
					],
				],
			]) ?>
		</div>
	</div>


</div>
