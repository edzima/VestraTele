<?php

use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @var DataProviderInterface $toDoDataProvider */
/** @var DataProviderInterface $toConfirmDataProvider */
/** @var DataProviderInterface $confirmedDataProviced */

?>

<div class="summon-docs-widget">
	<div class="col-md-4">
		<?= GridView::widget([
			'dataProvider' => $toDoDataProvider,
		]) ?>
	</div>

	<div class="col-md-4">

	</div>

	<div class="col-md-4">

	</div>
</div>
