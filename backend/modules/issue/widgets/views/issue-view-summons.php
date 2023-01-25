<?php

use backend\modules\issue\widgets\SummonGrid;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $caption string */
/* @var $realizedDataProvider DataProviderInterface */
/* @var $withoutRealizedProvider DataProviderInterface */
/* @var $realizedId string */
?>


<?= SummonGrid::widget([
	'caption' => $caption,
	'showOnEmpty' => true,
	'emptyText' => '',
	'dataProvider' => $withoutRealizedProvider,
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

<?php if (!empty($realizedDataProvider->getModels())): ?>

	<div class="collapse" id="<?= $realizedId ?>">

		<?= SummonGrid::widget([
			'showOnEmpty' => false,
			'emptyText' => '',
			'dataProvider' => $realizedDataProvider,
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
