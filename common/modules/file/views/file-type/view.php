<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\file\models\FileType $model */

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

	</div>


</div>
