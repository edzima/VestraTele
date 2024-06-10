<?php

use common\modules\file\models\FileType;
use common\modules\file\models\VisibilityOptions;
use common\widgets\grid\IssuesDataColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var FileType $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'File Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="file-type-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('file', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('file', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('file', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-4">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'name',
					'is_active:boolean',
					'visibilityName',
				],
			]) ?>

		</div>
		<div class="col-md-2">

			<?= DetailView::widget([
				'model' => $model->getValidatorOptions(),
				'attributes' => [
					'maxSize',
					'maxFiles',
					'extensions',
				],
			]) ?>
		</div>

		<div class="col-md-2">

			<?= DetailView::widget([
				'model' => $model->getVisibilityOptions(),
				'attributes' => [
					[
						'attribute' => 'allowedRoles',
						'format' => 'html',
						'value' => function (VisibilityOptions $model): ?string {
							return Html::ul($model->allowedRoles);
						},
						'visible' => !empty($model->getVisibilityOptions()->allowedRoles),
					],
					[
						'attribute' => 'disallowedRoles',
						'format' => 'html',
						'value' => function (VisibilityOptions $model): ?string {
							return Html::ul($model->disallowedRoles);
						},
						'visible' => !empty($model->getVisibilityOptions()->disallowedRoles),
					],
				],
			]) ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<?= GridView::widget([
				'dataProvider' => (new ActiveDataProvider([
					'query' => $model->getFiles(),
				])),
				'columns' => [
					'name',
					'type',
					[
						'attribute' => 'size',
						'value' => 'formattedSize',
					],
					[
						'class' => IssuesDataColumn::class,
					],
				],
			]) ?>
		</div>

	</div>


</div>
