<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\modules\file\models\FileAccess $model */

$this->title = $model->file_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'File Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="file-access-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('file', 'Update'), ['update', 'file_id' => $model->file_id, 'user_id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('file', 'Delete'), ['delete', 'file_id' => $model->file_id, 'user_id' => $model->user_id], [
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
			'file_id',
			'user_id',
			'access',
		],
	]) ?>

</div>
