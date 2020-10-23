<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\Customer;
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
		<?= Html::a(Yii::t('backend', 'Add issue'), ['/issue/issue/create', 'customerId' => $model->id], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('backend', 'Link to issue'), ['/issue/user/link', 'userId' => $model->id], ['class' => 'btn btn-success']) ?>
		<?php //@todo add this action
		//  Html::a(Yii::t('backend', 'Generate password'), ['/issue/user/link', 'userId' => $model->id], ['class' => 'btn btn-success']) ?>
	</p>


	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'email',
			'profile.phone',
			'profile.phone_2',
			'statusName',
			'username',
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


	<fieldset>
		<legend><?= Yii::t('common', 'Issues') ?></legend>

		<?= GridView::widget([
			'dataProvider' => $issuesDataProvider,
			'columns' => [
				['class' => IssueColumn::class],
				[
					'attribute' => 'issue.signature_act',
					'label' => Issue::instance()->getAttributeLabel('signature_act'),
				],
				[
					'attribute' => 'typeName',
					'label' => Yii::t('common', 'As role'),
				],
				[
					'attribute' => 'issue.type',
					'label' => Issue::instance()->getAttributeLabel('type'),
				],
				[
					'attribute' => 'issue.stage',
					'label' => Issue::instance()->getAttributeLabel('stage'),
				],
				[
					'attribute' => 'issue.agent.username',
					'value' => function (IssueUser $model): ?string {
						// @todo remove this condition after full upgrade.
						if ($model->issue->agent) {
							return $model->issue->agent->username;
						}
						return null;
//						/return $model->issue->agent->username;
					},

					'label' => Issue::instance()->getAttributeLabel('agent'),
				],
				[
					'attribute' => 'issue.updated_at',
					'format' => 'date',
					'label' => Issue::instance()->getAttributeLabel('updated_at'),
				],

			],
		]) ?>

	</fieldset>


</div>

