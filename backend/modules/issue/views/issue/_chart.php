<?php

use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\Module;
use common\helpers\ArrayHelper;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
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

/**
 * @var IssueQuery $stageQuery
 */
$agentQuery = clone $dataProvider->query;
$agentQuery->with = [];
$agentQuery->joinWith = [];

$agentData = $agentQuery
	->select([
		'user_id', 'count(*) as agentCount',
	])
	->joinWith('agent')
	->groupBy('user_id')
	->asArray()
	->orderBy('agentCount DESC')
	->all();

$agentChartData = [];
$agentsNamesWithCount = [];
$agentsIds = ArrayHelper::getColumn($agentData, 'user_id');
if (!empty($agentsIds)) {
	$agentsNames = User::getSelectList($agentsIds, false);
	foreach ($agentData as $data) {
		$agentId = $data['user_id'];
		$agentName = $agentsNames[$agentId];
		$agentCount = (int) $data['agentCount'];
		$agentChartData['labels'][] = $agentName;
		$agentChartData['series'][] = $agentCount;
		$agentsNamesWithCount[$agentId] = "$agentName ($agentCount)";
	}
}

?>
<div id="issue-chart" class="issue-chart collapse<?= $model->getIsLoad() ? ' in' : '' ?>">

	<div class="row">

		<?= !empty($typeChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_PIE,
				'id' => 'issue-chart-type-chart',
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $typeChartData['series'],
				'options' => [
					'labels' => $typeChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
					'title' => [
						'text' => Yii::t('issue', 'Types Count'),
						'align' => 'center',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4',
					'style' => ['height' => '50vh',],
				],
			])
			: ''
		?>

		<?= !empty($stageChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_PIE,
				'id' => 'issue-chart-stage-chart',
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $stageChartData['series'],
				'options' => [
					'labels' => $stageChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
					'title' => [
						'text' => Yii::t('issue', 'Stages Count'),
						'align' => 'center',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4',
					'style' => ['height' => '50vh',],
				],
			])
			: ''
		?>

		<?= !empty($entityChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_PIE,
				'id' => 'issue-chart-entity-responsible-chart',
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $entityChartData['series'],
				'options' => [
					'labels' => $entityChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
					'title' => [
						'text' => Yii::t('issue', 'Entity Responsible Count'),
						'align' => 'center',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4',
					'style' => ['height' => '50vh',],
				],
			])
			: ''
		?>


	</div>

	<?php if (!empty($agentData)): ?>
		<div class="row">

			<?= ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_AREA,
				'height' => '500',
				'series' => [
					[
						'name' => 'Umowy',
						'data' => $agentChartData['series'],
						'type' => 'bar',
					],
				],
				'options' => [
					'labels' => $agentChartData['labels'],
					'plotOptions' => [
						'bar' => [
							'dataLabels' => [
								'total' => [
									'enabled' => true,
								],
							],
						],
					],
					'stroke' => [
						'width' => 0,
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-8',
					'style' => ['height' => '50vh',],
				],
			]) ?>

			<?=
			ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_DONUT,
				'showDonutTotalLabels' => true,
				'legendFormatterAsSeriesWithCount' => true,
				'series' => $agentChartData['series'],
				'options' => [
					'labels' => $agentChartData['labels'],
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
					'title' => [
						'text' => Yii::t('issue', 'Agent'),
						'align' => 'center',
					],

				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-4',
					'style' => ['height' => '50vh',],
				],
			])
			?>

		</div>
	<?php endif; ?>

</div>
