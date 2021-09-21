<?php

use common\helpers\Html;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadReportSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
				'attribute' => 'lead_name',
				'value' => 'lead.name',
				'label' => Yii::t('lead', 'Lead Name'),
			],
			[
				'attribute' => 'lead_phone',
				'value' => 'lead.phone',
				'format' => 'tel',
				'label' => Yii::t('lead', 'Phone'),
			],
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
				'template' => '{report} {view} {update} {delete}',
				'buttons' => [
					'report' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('comment'),
							['report', 'id' => $report->lead_id],
							[
								'title' => Yii::t('lead', 'Create Report'),
								'aria-title' => Yii::t('lead', 'Create Report'),
							]
						);
					},
					'view' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('eye-open'),
							['lead/view', 'id' => $report->lead_id], [
								'title' => Yii::t('yii', 'View'),
								'aria-title' => Yii::t('yii', 'View'),
							]
						);
					},
				],
			],
		],
	]); ?>


</div>
