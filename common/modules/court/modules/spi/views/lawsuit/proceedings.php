<?php

use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @see LawsuitProceedingDTO */
/** @var DataProviderInterface $dataProvider */

?>

<div class="lawsuit-proceedings">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'summary' => false,
		'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'refreshGrid' => true,
		],
		'columns' => [
			'name',
			'date:date',
			'sender',
			'createdDate:datetime',
			'modificationDate:datetime',
		],
	]) ?>
</div>
