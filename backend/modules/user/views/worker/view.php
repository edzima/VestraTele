<?php

use common\models\user\Worker;
use common\widgets\address\AddressDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Worker */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?=
		Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
			? Html::a(Yii::t('backend', 'Assign supervisor'), ['hierarchy', 'id' => $model->id], ['class' => 'btn btn-info'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE)
			? Html::a(
				Yii::t('backend', 'Link to issue'),
				['/issue/user/link', 'userId' => $model->id],
				['class' => 'btn btn-success']
			)
			: ''
		?>

		<?php if (Yii::$app->user->can(Worker::PERMISSION_WORKERS)): ?>
			<?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			]) ?>
		<?php endif; ?>


	</p>


	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'username',
				'visible' => Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR),
			],
			'profile.firstname',
			'profile.lastname',
			'email',
			'profile.phone',
			'profile.phone_2',
			'statusName',
		],
	]) ?>

	<?= $model->homeAddress ? AddressDetailView::widget(['model' => $model->homeAddress]) : '' ?>

</div>

