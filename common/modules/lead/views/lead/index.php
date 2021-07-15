<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\searches\LeadSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AddressColumn;
use common\widgets\GridView;
use kartik\grid\CheckboxColumn;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $assignUsers bool */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Leads');
$this->params['breadcrumbs'][] = $this->title;

$questionColumns = [];
foreach (LeadSearch::questions() as $question) {
	//@todo add input placeholders from $question->placeholder
	$questionColumns[] = [
		'attribute' => LeadSearch::generateQuestionAttribute($question->id),
		'label' => $question->name,
		'format' => $question->hasPlaceholder() ? 'text' : 'boolean',
		'value' => static function (ActiveLead $lead) use ($question): ?string {
			if ($question->hasPlaceholder()) {
				return $lead->answers[$question->id]->answer ?? null;
			}
			return isset($lead->answers[$question->id]);
		},
	];
}
?>
<div class="lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?php if ($assignUsers): ?>

		<?= Html::beginForm('', 'POST', [
			'id' => 'user-assign-form',
			'data-pjax' => '',
		]) ?>

		<?= Html::submitButton(
			Yii::t('lead', 'Link users'),
			[
				'class' => 'btn btn-success',
				'id' => 'assign-action-btn',
			])
		?>

	<?php endif; ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'id' => 'leads-grid',
		'columns' => array_merge([
			[
				'class' => CheckboxColumn::class,
				'visible' => $assignUsers,
			],
			[
				'attribute' => 'owner_id',
				'label' => Yii::t('lead', 'Owner'),
				'value' => 'owner',
			],
			'name',
			[
				'attribute' => 'type_id',
				'value' => 'source.type',
				'filter' => $searchModel::getTypesNames(),
				'label' => Yii::t('lead', 'Type'),
			],
			[
				'attribute' => 'status_id',
				'value' => 'status',
				'filter' => $searchModel::getStatusNames(),
				'label' => Yii::t('lead', 'Status'),
			],
			[
				'attribute' => 'source_id',
				'value' => 'source',
				'filter' => $searchModel->getSourcesNames(),
				'label' => Yii::t('lead', 'Source'),
			],
			[
				'attribute' => 'campaign_id',
				'value' => 'campaign',
				'filter' => $searchModel->getCampaignNames(),
				'label' => Yii::t('lead', 'Campaign'),
			],
			'date_at',
			'phone:tel',
		],
			$questionColumns,
			[
				[
					'class' => AddressColumn::class,
					'attribute' => 'customerAddress',
				],
				[
					'attribute' => 'reportsCount',
					'value' => static function (ActiveLead $lead): int {
						return count($lead->reports);
					},
					'filter' => $searchModel::getStatusNames(),
					'label' => Yii::t('lead', 'Reports'),
				],
				[
					'class' => ActionColumn::class,
					'template' => '{view} {update} {report} {reminder} {delete}',
					'visibleButtons' => $visibleButtons,
					'buttons' => [
						'report' => static function (string $url, ActiveLead $lead): string {
							return Html::a(Html::icon('comment'), ['report/report', 'id' => $lead->getId()]);
						},
						'reminder' => static function (string $url, ActiveLead $lead): string {
							return Html::a(Html::icon('calendar'), ['reminder/create', 'id' => $lead->getId()]);
						},
					],
				],
			]),
	]) ?>

	<?= $assignUsers ? Html::endForm() : '' ?>

</div>
