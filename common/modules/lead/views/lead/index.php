<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\searches\LeadSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Leads');
$this->params['breadcrumbs'][] = $this->title;

$schemas = LeadQuestion::find()
	->andWhere(['show_in_grid' => true])
	->all();

$schemasColumns = [];
foreach ($schemas as $schema) {
	$schemasColumns[] = [
		'attribute' => $schema->name,
		'value' => function (ActiveLead $lead) use ($schema): ?string {
			return $lead->reports[$schema->id]->details;
		},
	];
}
?>
<div class="lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => array_merge([
			['class' => 'yii\grid\SerialColumn'],
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
			'email:email',
			'postal_code',
			[
				'attribute' => 'provider',
				'value' => 'providerName',
			],
			'owner',

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
				'template' => '{view} {update} {report} {delete}',
				'buttons' => [
					'report' => static function (string $url, ActiveLead $lead): string {
						return Html::a(Html::icon('comment'), ['report/report', 'id' => $lead->getId()]);
					},
				],
			],
		], $schemasColumns),
	]); ?>


</div>
