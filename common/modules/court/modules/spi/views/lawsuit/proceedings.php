<?php

use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @var DataProviderInterface $dataProvider */

?>

<div class="lawsuit-sessions">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			'name',
			'date:date',
			'sender',
			'createdDate:datetime',
			'modificationDate:datetime',
		],
	]) ?>
</div>
