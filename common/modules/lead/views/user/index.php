<?php

use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadUsersSearch;
use common\modules\lead\Module;
use yii\helpers\Html;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Users');
?>
<div class="lead-user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead User'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('lead', 'Delete'), false, [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'method' => 'delete',
				'confirm' => Yii::t('lead', 'Are you sure you want to delete this assigned users?'),
			],
		]) ?>

	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'lead_id',
			[
				'attribute' => 'leadName',
				'value' => 'lead.name',
			],
			[
				'attribute' => 'leadStatusId',
				'value' => 'lead.statusName',
				'label' => Yii::t('lead', 'Status'),
				'filter' => LeadStatus::getNames(),
			],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => Module::userNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('user_id'),
					],
				],
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => LeadUsersSearch::getTypesNames(),
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
