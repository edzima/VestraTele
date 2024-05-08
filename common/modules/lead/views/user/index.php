<?php

use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadUsersSearch;
use common\modules\lead\Module;
use common\widgets\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

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


	<?= $this->render('_search', ['model' => $searchModel]); ?>


	<div class="row">
		<div class="col-sm-3">
			<?= DetailView::widget([
				'model' => $searchModel->getAvgViewDuration(),
				'attributes' => [
					[
						'attribute' => 'firstViewDuration',
						'format' => 'duration',
					],
					[
						'attribute' => 'viewDuration',
						'format' => 'duration',
					],
				],
			]) ?>
		</div>

	</div>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'leadName',
				'format' => 'html',
				'value' => function (LeadUser $data): string {
					return Html::a(Html::encode($data->lead->getName()), [
						'lead/view', 'id' => $data->lead_id,
					]);
				},
				'label' => Yii::t('lead', 'Lead'),
			],
			[
				'attribute' => 'leadTypeId',
				'value' => 'lead.typeName',
				'label' => Yii::t('lead', 'Type'),
				'filter' => LeadType::getNames(),
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
			'created_at:datetime',
			'first_view_at:datetime',
			[
				'attribute' => 'firstViewDuration',
				//	'value' => 'firstViewDuration',
				'format' => 'duration',
				'filter' => \common\helpers\Html::booleanDropdownList(),
			],
			'last_view_at:datetime',
			[
				'attribute' => 'lastViewDuration',
				'format' => 'duration',
			],
			'action_at:datetime',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
