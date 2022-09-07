<?php

use common\models\user\User;
use common\widgets\address\AddressDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Yii::$app->user->can(User::PERMISSION_ISSUE)
			? Html::a(
				Yii::t('backend', 'Link to issue'),
				['/issue/user/link', 'userId' => $model->id],
				['class' => 'btn btn-success']
			)
			: ''
		?>

		<?= Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>


	<?= DetailView::widget([
		'model' => $model,
	]) ?>

	<?= $model->homeAddress ? AddressDetailView::widget(['model' => $model->homeAddress]) : '' ?>

</div>

