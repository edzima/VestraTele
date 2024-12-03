<?php

use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\Module;
use common\models\issue\query\IssueQuery;
use common\widgets\charts\ChartsWidget;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model IssueSearch */
/* @var $dataProvider ActiveDataProvider */

if (!$model->showChart || !Yii::$app->user->can(Module::PERMISSION_ISSUE_CHART)) {
	return '';
}

/**
 * @var IssueQuery $stageQuery
 */
$stageQuery = clone $dataProvider->query;
$stageQuery->with = [];
$stageQuery->joinWith = [];

$stagesData = $stageQuery
	->select([
		'stage_id', 'count(*) as stageCount',
	])
	->joinWith('stage')
	->groupBy('stage_id')
	->asArray()
	->orderBy('stageCount DESC')
	->all();

$stagesNamesWithCount = [];
$stageChartData = [];
foreach ($stagesData as $stageData) {
	$stageName = $stageData['stage']['name'];
	$stageCount = (int) $stageData['stageCount'];
	$stageChartData['labels'][] = $stageName;
	$stageChartData['series'][] = $stageCount;
	$stagesNamesWithCount[$stageData['stage_id']] = "$stageName ($stageCount)";
}

$model->setStagesNames($stagesNamesWithCount);

/**
 * @var IssueQuery $stageQuery
 */
$entityQuery = clone $dataProvider->query;
$entityQuery->with = [];
$entityQuery->joinWith = [];

$entityData = $entityQuery
	->select([
		'entity_responsible_id', 'count(*) as entityCount',
	])
	->joinWith('entityResponsible')
	->groupBy('entity_responsible_id')
	->asArray()
	->orderBy('entityCount DESC')
	->all();

$entityNamesWithCount = [];
$entityChartData = [];
foreach ($entityData as $data) {
	$entityName = $data['entityResponsible']['name'];
	$entityCount = (int) $data['entityCount'];
	$entityChartData['labels'][] = $entityName;
	$entityChartData['series'][] = $entityCount;
	$entityNamesWithCount[$data['entity_responsible_id']] = "$entityName ($entityCount)";
}

$model->setEntityResponsibleNames($entityNamesWithCount);

/**
 * @var IssueQuery $stageQuery
 */
$typeQuery = clone $dataProvider->query;
$typeQuery->with = [];
$typeQuery->joinWith = [];

$typesData = $stageQuery
	->select([
		'type_id', 'count(*) as typeCount',
	])
	->joinWith('type')
	->groupBy('type_id')
	->asArray()
	->orderBy('typeCount DESC')
	->all();

$typesNamesWithCount = [];
$typeChartData = [];
foreach ($typesData as $typeData) {
	$typeName = $typeData['type']['name'];
	$typeCount = (int) $typeData['typeCount'];
	$typeChartData['labels'][] = $typeName;
	$typeChartData['series'][] = $typeCount;
	$typesNamesWithCount[$typeData['type_id']] = "$typeName ($typeCount)";
}

$model->setTypesNames($typesNamesWithCount);
?>
<div id="issue-chart" class="issue-chart collapse<?= $model->getIsLoad() ? ' in' : '' ?>">

	<div class="row">

		<?= !empty($typeChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_DONUT,
				'id' => 'issue-chart-type--chart',
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $typeChartData['series'],
				'options' => [
					'labels' => $typeChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
					'style' => ['height' => '50vh',],
				],
			])
			: ''
		?>

		<?= !empty($stageChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_DONUT,
				'id' => 'issue-chart-stage-chart',
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $stageChartData['series'],
				'options' => [
					'labels' => $stageChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
					'style' => ['height' => '50vh',],
				],
			])
			: ''
		?>

		<?= !empty($entityChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_DONUT,
				'id' => 'issue-chart-entity-responsible-chart',
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $entityChartData['series'],
				'options' => [
					'labels' => $entityChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
					'style' => ['height' => '50vh',],
				],
			])
			: ''
		?>


	</div>

</div>
