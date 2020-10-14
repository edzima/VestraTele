<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\Customer;
use common\widgets\address\AddressDetailView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Customer */
/* @var $issuesDataProvider ActiveDataProvider */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Add issue'), ['/issue/issue/create', 'customerId' => $model->id], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('backend', 'Link to issue'), ['/issue/user/link', 'userId' => $model->id], ['class' => 'btn btn-success']) ?>

	</p>


	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'profile.firstname',
			'profile.lastname',
			'email',
			'profile.phone',
			'profile.phone_2',
			'statusName',
		],
	]) ?>

	<?= $model->homeAddress ? AddressDetailView::widget(['model' => $model->homeAddress]) : '' ?>

	<fieldset>
		<legend><?= Yii::t('common', 'Issues') ?></legend>

		<?= GridView::widget([
			'dataProvider' => $issuesDataProvider,
			'columns' => [
				['class' => IssueColumn::class],
				'typeName',
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

