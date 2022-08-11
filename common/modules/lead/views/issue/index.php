<?php

use common\models\issue\IssueStage;
use common\modules\lead\models\LeadIssue;
use common\modules\lead\models\searches\LeadIssueSearch;
use common\modules\lead\widgets\LeadIssueActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadIssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Issues');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-issue-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'leadName',
				'value' => static function (LeadIssue $data): string {
					return Html::a(Html::encode($data->lead->getName()), [
						'/lead/lead/view', 'id' => $data->lead_id,
					]);
				},
				'format' => 'html',
			],
			[
				'attribute' => 'leadStatus',
				'value' => 'lead.statusName',
				'filter' => LeadIssueSearch::getLeadStatusesNames(),
			],
			[
				'attribute' => 'issue_id',
				'value' => function (LeadIssue $data) use ($searchModel): string {
					if ($searchModel->isCurrentCrmApp()) {
						return Html::a(
							Html::encode($data->issue->getIssueName()),
							['/issue/issue/view', 'id' => $data->issue_id]
						);
					}
					return Html::a($data->issue_id,
						$data->getIssueBackendUrl(),
						[
							'target' => '_blank',
						]
					);
				},
				'format' => 'raw',
			],
			[
				'attribute' => 'issueStage',
				'visible' => $searchModel->isCurrentCrmApp(),
				'value' => function (LeadIssue $data): ?string {
					if ($data->issue) {
						return IssueStage::getStages()[$data->issue->getIssueStageId()];
					}
					return null;
					return IssueStage::getStages()[$data->issue->getIssueStageId()];
				},
				'filter' => $searchModel->getIssueStagesNames(),
			],
			[
				'attribute' => 'crm_id',
				'value' => 'crm.name',
				'filter' => LeadIssueSearch::getCrmsNames(),
			],
			'created_at:datetime',
			'updated_at:datetime',
			'confirmed_at:datetime',
			[
				'class' => LeadIssueActionColumn::class,
			],
		],
	]); ?>


</div>
