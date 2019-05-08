<?php

/** @var $title string */

use common\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $legend string */
/** @var $dataProvider ActiveDataProvider */
?>
<fieldset>
	<legend><?= $legend ?></legend>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'format' => 'raw',
				'attribute' => 'fullName',
				'value' => function (User $data) {
					return Html::a($data->getFullName(), ['update', 'id' => $data->id], ['target' => '_blank']);
				},
			],
			'action_at:datetime',
			'email:email',
		],

	]); ?>
</fieldset>

