<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\Worker;
use common\widgets\address\AddressDetailView;
use common\widgets\FieldsetDetailView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Worker */
/* @var $issuesDataProvider ActiveDataProvider */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?=
		Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
			? Html::a(Yii::t('common', 'Hierarchy'), ['hierarchy', 'id' => $model->id], ['class' => 'btn btn-info'])
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
				'label' => Yii::t('backend', 'Email'),
				'value' => $model->email,
				'format' => 'email',
				'visible' => !empty($model->email),
			],
			[
				'label' => Yii::t('common', 'Phone number'),
				'value' => $model->profile->phone,
				'visible' => !empty($model->profile->phone),
			],
			[
				'label' => Yii::t('common', 'Phone number 2'),
				'value' => $model->profile->phone_2,
				'visible' => !empty($model->profile->phone_2),
			],
			[
				'label' => Yii::t('backend', 'Traits'),
				'value' => $model->getTraitsNames(),
				'visible' => !empty($model->traits),
			],
			[
				'label' => Yii::t('backend', 'Status'),
				'value' => $model->getStatusName(),
			],
			[
				'label' => Yii::t('backend', 'Username'),
				'value' => $model->username,
				'visible' => Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR),
			],
			[
				'label' => Yii::t('backend', 'Other'),
				'value' => $model->profile->other,
				'visible' => !empty($model->profile->other),

			],
		],
	]) ?>

	<div class="row">
		<?= FieldsetDetailView::widget([
			'legend' => Yii::t('common', 'Home address'),
			'detailConfig' => [
				'class' => AddressDetailView::class,
				'model' => $model->homeAddress,
			],
			'htmlOptions' => [
				'class' => 'col-md-6',
			],
		]) ?>

		<?= FieldsetDetailView::widget([
			'legend' => Yii::t('common', 'Postals address'),
			'detailConfig' => [
				'class' => AddressDetailView::class,
				'model' => $model->postalAddress,
			],
			'htmlOptions' => [
				'class' => 'col-md-6',
			],
		]) ?>


	</div>

	<?= $model->homeAddress ? AddressDetailView::widget(['model' => $model->homeAddress]) : '' ?>

</div>

