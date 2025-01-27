<?php

use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @var DataProviderInterface $dataProvider */

?>

<div class="documents-lawsuit">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			'documentName',
			'createdDate:datetime',
			'modificationDate:datetime',
			'downloaded:boolean',
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
			],
		],
	]) ?>
</div>
