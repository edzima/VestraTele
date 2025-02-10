<?php

use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @see LawsuitSessionDTO */
/** @var DataProviderInterface $dataProvider */

?>

<div class="lawsuit-sessions">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'summary' => false,
		'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'refreshGrid' => true,
		],
		'columns' => [
			'result',
			'date:datetime',
			'room',
			'judge',
			'createdDate:datetime',
			'modificationDate:datetime',
		],
	]) ?>
</div>
