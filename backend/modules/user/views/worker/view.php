<?php

use backend\helpers\Url;
use backend\modules\user\widgets\CopyToCliboardFormAttributesBtn;
use common\models\user\Worker;
use common\widgets\address\AddressDetailView;
use common\widgets\FieldsetDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Worker */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
	<p>

		<?= CopyToCliboardFormAttributesBtn::widget([
			'model' => $model,
		])
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE)
			? Html::a(
				Yii::t('backend', 'Link to issue'),
				['/issue/user/link', 'userId' => $model->id],
				['class' => 'btn btn-success']
			)
			: ''
		?>


		<?=
		Yii::$app->user->can(Worker::PERMISSION_PROVISION)
			? Html::a(Yii::t('provision', 'Schemas provisions'), Url::userProvisions($model->id), ['class' => 'btn btn-success'])
			: ''
		?>


		<?=
		Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
			? Html::a(Yii::t('backend', 'Assign supervisor'), ['hierarchy', 'id' => $model->id], ['class' => 'btn btn-info'])
			: ''
		?>


		<?php if (Yii::$app->user->can(Worker::PERMISSION_WORKERS)): ?>
			<?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger pull-right',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			]) ?>
		<?php endif; ?>


	</p>

	<div class="row">
		<div class="col-md-4">
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
						'format' => 'tel',
					],
					[
						'label' => Yii::t('common', 'Phone number 2'),
						'value' => $model->profile->phone_2,
						'visible' => !empty($model->profile->phone_2),
						'format' => 'tel',
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

		</div>
		<div class="col-md-6">
			<?= $model->homeAddress ? FieldsetDetailView::widget([
				'legend' => Yii::t('common', 'Home address'),
				'detailConfig' => [
					'class' => AddressDetailView::class,
					'model' => $model->homeAddress,
				],
				'htmlOptions' => [
					'class' => 'col-md-6',
				],
			])
				: ''
			?>

			<?= $model->postalAddress ? FieldsetDetailView::widget([
				'legend' => Yii::t('common', 'Postal address'),
				'detailConfig' => [
					'class' => AddressDetailView::class,
					'model' => $model->postalAddress,
				],
				'htmlOptions' => [
					'class' => 'col-md-6',
				],
			])
				: ''
			?>


		</div>
	</div>


</div>

