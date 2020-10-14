<?php

use common\models\user\User;
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
		<?php if (Yii::$app->user->can(\common\models\user\User::ROLE_ADMINISTRATOR)): ?>
			<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
			<?= Html::a('Usuń', ['delete', 'id' => $model->id], [
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
				'visible' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
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

