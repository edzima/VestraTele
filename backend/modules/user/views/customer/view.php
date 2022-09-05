<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\user\Customer;
use common\models\user\User;
use common\widgets\address\AddressDetailView;
use common\widgets\FieldsetDetailView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Customer */
/* @var $issuesDataProvider ActiveDataProvider */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Yii::$app->user->can(User::PERMISSION_ISSUE)
			? Html::a(
				Yii::t('backend', 'Add issue'),
				['/issue/issue/create', 'customerId' => $model->id],
				['class' => 'btn btn-success']
			)
			: ''
		?>

		<?= Yii::$app->user->can(User::PERMISSION_ISSUE)
			? Html::a(
				Yii::t('backend', 'Link to issue'),
				['/issue/user/link', 'userId' => $model->id],
				['class' => 'btn btn-success']
			)
			: ''
		?>

		<?php //@todo add this action
		//  Html::a(Yii::t('backend', 'Generate password'), ['/issue/user/link', 'userId' => $model->id], ['class' => 'btn btn-success']) ?>
	</p>

	<div class="row">
		<div class="col-md-6">
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
						'attribute' => 'profile.pesel',
						'label' => Yii::t('backend', 'PESEL'),
						'visible' => !empty($model->profile->pesel),
					],
					[
						'label' => Yii::t('settlement', 'Tax Office'),
						'value' => $model->profile->tax_office,
						'visible' => !empty($model->profile->tax_office),
					],
					[
						'label' => Yii::t('backend', 'Username'),
						'value' => $model->username,
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
			<?= FieldsetDetailView::widget([
				'legend' => Yii::t('common', 'Home address'),
				'detailConfig' => [
					'class' => AddressDetailView::class,
					'model' => $model->homeAddress,
				],

			]) ?>

			<?= FieldsetDetailView::widget([
				'legend' => Yii::t('common', 'Postals address'),
				'detailConfig' => [
					'class' => AddressDetailView::class,
					'model' => $model->postalAddress,
				],
			]) ?>

		</div>
	</div>


	<fieldset>
		<legend><?= Yii::t('common', 'Issues') ?></legend>

		<?= GridView::widget([
			'dataProvider' => $issuesDataProvider,
			'columns' => [
				['class' => IssueColumn::class],
				[
					'attribute' => 'issue.signature_act',
				],
				[
					'attribute' => 'typeName',
					'label' => Yii::t('common', 'As role'),
				],
				[
					'attribute' => 'issue.type',
				],
				[
					'attribute' => 'issue.stage',
				],
				[
					'attribute' => 'issue.entityResponsible',
				],
				[
					'attribute' => 'issue.agent',
					'label' => Issue::instance()->getAttributeLabel('agent'),
				],
				[
					'attribute' => 'issue.updated_at',
					'format' => 'date',
				],
			],
		])
		?>

	</fieldset>


</div>

