<?php

/** @var $title string */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\user\Worker;

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
				'value' => static function (Worker $data) {
					return Html::a($data->getFullName(), ['hierarchy', 'id' => $data->id], ['target' => '_blank']);
				},
			],
			[
				'format' => 'raw',
				'label' => Yii::t('common', 'Parent'),
				'value' => static function (Worker $data): string {
					if ($data->parent) {
						return Html::a($data->parent->getFullName(), ['hierarchy', 'id' => $data->parent->id], ['target' => '_blank']);
					}
					return '';
				},
			],
		],

	]); ?>
</fieldset>

