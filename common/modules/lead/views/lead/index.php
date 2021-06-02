<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\searches\LeadSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AddressColumn;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => array_merge([
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
				'filter' => $searchModel::getSourcesNames(),
				'label' => Yii::t('lead', 'Source'),
			],
			'date_at',
			'phone',
		],
			$questionColumns,
			[
				[
					'class' => AddressColumn::class,
					'attribute' => 'customerAddress',
				],
				[
					'attribute' => 'reportsCount',
					'value' => function (ActiveLead $lead): int {
						return count($lead->reports);
					},
					'filter' => $searchModel::getStatusNames(),
					'label' => Yii::t('lead', 'Reports'),
				],
				[
					'class' => ActionColumn::class,
					'template' => '{view} {update} {report} {reminder} {delete}',
					'buttons' => [
						'report' => static function (string $url, ActiveLead $lead): string {
							return Html::a(Html::icon('comment'), ['report/report', 'id' => $lead->getId()]);
						},
						//@todo find icon for reminder.
						'reminder' => static function (string $url, ActiveLead $lead): string {
							return Html::a(Html::icon('comment'), ['reminder/create', 'id' => $lead->getId()]);
						},
					],
				],
			]),
	]); ?>


</div>
