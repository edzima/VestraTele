<?php

use common\models\user\UserRelation;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model UserRelation */

$this->title = $model->user->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-relation-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id, 'type' => $model->type], [
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
			'toUser',
			'typeName',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

</div>
