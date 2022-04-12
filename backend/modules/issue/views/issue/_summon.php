<?php

use backend\helpers\Html;
use backend\modules\issue\widgets\SummonGrid;
use common\models\issue\Summon;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

/** @var DataProviderInterface $dataProvider */

$modelsWithoutRealized = [];
$modelsRealized = [];
foreach ($dataProvider->getModels() as $model) {
	/** @var Summon $model */
	if ($model->isRealized()) {
		$modelsRealized[$model->id] = $model;
	} else {
		$modelsWithoutRealized[$model->id] = $model;
	}
}
$caption = Yii::t('issue', 'Summons');
if (!empty($modelsRealized)) {

	$caption .= Html::button(Yii::t('issue', 'Realized: {count}', [
		'count' => count($modelsRealized),
	]), [
		'class' => 'btn btn-success btn-sm pull-right',
		'data' => [
			'toggle' => 'collapse',
			'target' => '#realizeSummons',
		],
		'aria-expanded' => false,
		'aria-controls' => 'realizeSummons',
	]);
}
?>

<?= SummonGrid::widget([
	'caption' => $caption,
	'showOnEmpty' => !empty($dataProvider->getModels()),
	'emptyText' => '',
	'dataProvider' => new ArrayDataProvider([
		'models' => $modelsWithoutRealized,
	]),
	'summary' => '',
	'withTitle' => false,
	'withDocs' => false,
	'withTitleWithDocs' => true,
	'withCaption' => true,
	'withCustomerPhone' => false,
	'withIssue' => false,
	'withCustomer' => false,
	'withOwner' => false,
	'withContractor' => true,
	'withUpdatedAt' => false,
]) ?>

<?php if (!empty($modelsRealized)): ?>

	<div class="collapse" id="realizeSummons">

		<?= SummonGrid::widget([
			'showOnEmpty' => false,
			'emptyText' => '',
			'dataProvider' => new ArrayDataProvider([
				'models' => $modelsRealized,
			]),
			'rowOptions' => [
				'class' => 'success',
			],
			'withCaption' => false,
			'summary' => '',
			'withStatus' => false,
			'withDeadline' => false,
			'withTitle' => false,
			'withDocs' => false,
			'withTitleWithDocs' => true,
			'withCustomerPhone' => false,
			'withIssue' => false,
			'withCustomer' => false,
			'withOwner' => false,
			'withContractor' => true,
			'withUpdatedAt' => false,
			'withRealizedAt' => true,
		]) ?>
	</div>

<?php endif; ?>
