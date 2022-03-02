<?php

use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadDialerSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadDialerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Dialers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-dialer-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>

		<?= Html::a(Yii::t('lead', 'Types'), ['dialer-type/index'], ['class' => 'btn btn-info']) ?>

	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'type_id',
				'value' => 'type.name',
				'filter' => LeadDialerSearch::getTypesNames(),
			],
			[
				'attribute' => 'lead_id',
				'value' => static function (LeadDialer $model): string {
					return Html::a(
						Html::encode($model->lead->getName()),
						['lead/view', 'id' => $model->lead_id],
						['target' => '_blank']
					);
				},
				'format' => 'raw',
				'label' => Yii::t('lead', 'Lead'),
			],
			[
				'attribute' => 'leadStatusId',
				'value' => 'lead.statusName',
				'label' => Yii::t('lead', 'Lead Status'),
				'filter' => LeadStatus::getNames(),
			],
			[
				'attribute' => 'leadSourceId',
				'value' => 'lead.source.name',
				'label' => Yii::t('lead', 'Lead Source'),
				'visible' => $searchModel->leadSourceWithoutDialer,
				'filter' => $searchModel::getLeadSourcesNames(),
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadDialerSearch::getStatusesNames(),
			],
			[
				'value' => 'dialerStatusName',
				'filter' => LeadDialerSearch::getStatusesNames(),
				'label' => Yii::t('lead', 'Dialer Status'),
			],

			[
				'attribute' => 'priority',
				'value' => 'priorityName',
				'filter' => LeadDialerSearch::getPriorityNames(),
			],
			'created_at:datetime',
			'updated_at:datetime',
			'last_at:datetime',
			[
				'attribute' => 'attemptsCount',
				'noWrap' => true,
			],
			//	'attemptsCount',
			//'dialer_config:ntext',

			['class' => ActionColumn::class],
		],
	]); ?>


</div>
