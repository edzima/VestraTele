<?php

use common\models\user\UserVisible;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model UserVisible */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Visibles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="user-visible-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'user',
			'toUser',
			'statusName',
		],
	]) ?>

</div>
