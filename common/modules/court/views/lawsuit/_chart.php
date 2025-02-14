<?php

use common\modules\court\models\LawsuitSession;
use common\modules\court\models\query\LawsuitQuery;
use common\modules\court\models\search\LawsuitSearch;
use common\widgets\charts\ChartsWidget;
use yii\data\ActiveDataProvider;

/** @var yii\web\View $this */
/** @var LawsuitSearch $model */
/* @var $dataProvider ActiveDataProvider */

/**
 * @var LawsuitQuery $resultQuery
 */
$resultQuery = clone $dataProvider->query;
$resultData = $resultQuery
	->select(['result', 'count(*) as count'])
	->groupBy('result')
	->asArray()
	->orderBy('count DESC')
	->all();
$resultNamesWithCount = [];
$resultChartData = [];
if (count($resultData) > 1) {
	foreach ($resultData as $row) {
		$name = $row['result'];
		if (empty($name)) {
			$name = Yii::t('court', 'Without Result');
		}
		$count = (int) $row['count'];
		$resultChartData['labels'][] = $name;
		$resultChartData['series'][] = $count;
		$resultNamesWithCount[$name] = "$name ($count)";
	}
	$model->setResultNames($resultNamesWithCount);
}

/**
 * @var LawsuitQuery $resultQuery
 */
$sessionsResult = clone $dataProvider->query;
$sessionsResultData = $sessionsResult
	->select([LawsuitSession::tableName() . '.result', 'count(*) as count'])
	->joinWith('sessions')
	->groupBy(LawsuitSession::tableName() . '.result')
	->asArray()
	->orderBy('count DESC')
	->all();
$sessionResultChartData = [];
if (count($sessionsResultData) > 1) {
	foreach ($sessionsResultData as $row) {
		$name = $row['result'];
		if (empty($name)) {
			$name = Yii::t('court', 'Without Result');
		}
		$count = (int) $row['count'];
		$sessionResultChartData['labels'][] = $name;
		$sessionResultChartData['series'][] = $count;
	}
}

?>

<div class="court-lawsuit-chart">
	<div class="row">
		<?= !empty($resultChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_DONUT,
				'showDonutTotalLabels' => true,
				'legendFormatterAsSeriesWithCount' => true,
				'height' => 300,
				'series' => $resultChartData['series'],
				'options' => [
					'labels' => $resultChartData['labels'],
					'legend' => [
						'position' => 'right',
					],
					'title' => [
						'text' => Yii::t('court', 'Lawsuits'),
						'align' => 'center',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6',
					'style' => ['height' => '30vh',],
				],
			])
			: ''
		?>

		<?= !empty($sessionResultChartData)
			? ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_DONUT,
				'showDonutTotalLabels' => true,
				'legendFormatterAsSeriesWithCount' => true,
				'height' => 300,
				'series' => $sessionResultChartData['series'],
				'options' => [
					'labels' => $sessionResultChartData['labels'],
					'legend' => [
						'position' => 'right',
					],
					'title' => [
						'text' => Yii::t('court', 'Lawsuit Sessions'),
						'align' => 'center',
					],
				],
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6',
					'style' => ['height' => '30vh',],
				],
			])
			: ''
		?>
	</div>
</div>
