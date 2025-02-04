<?php

use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @var DataProviderInterface $dataProvider */

?>

<div class="lawsuit-sessions">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			'date:datetime',
			'room',
			'judge',
			'createdDate:datetime',
			'modificationDate:datetime',
		],
	]) ?>
</div>
