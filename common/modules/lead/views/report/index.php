<?php

use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadReportSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Lead Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Reports');
?>
<div class="lead-report-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'lead_type_id',
				'value' => 'lead.source.type',
				'filter' => LeadType::getNames(),
				'label' => Yii::t('lead', 'Lead Type'),
			],
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => LeadReportSearch::getOwnersNames(),
				'label' => Yii::t('lead', 'Owner'),
				'visible' => $searchModel->scenario !== LeadReportSearch::SCENARIO_OWNER,
			],
			[
				'attribute' => 'status_id',
				'value' => 'status',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Status'),

			],
			[
				'attribute' => 'old_status_id',
				'value' => 'oldStatus',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Old Status'),
			],
			'answersQuestions',
			'details:text',
			'created_at:date',
			'updated_at:date',

			[
				'class' => ActionColumn::class,
				'visibleButtons' => $visibleButtons,
			],
		],
	]); ?>


</div>
