<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\file\models\File $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="file-view">

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

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'name',
			'hash',
			'size',
			'type',
			'mime',
			'file_type_id',
			'created_at',
			'updated_at',
			'owner_id',
		],
	]) ?>

</div>
