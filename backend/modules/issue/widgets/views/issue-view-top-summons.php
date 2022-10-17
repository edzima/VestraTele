<?php

use backend\widgets\GridView;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $typesDataProviders DataProviderInterface[] */

?>

<?php foreach ($typesDataProviders as $typeId => $dataProvider): ?>
	<?php
	$type = SummonType::getModels()[$typeId];
	?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'caption' => $type->name,
		'showPageSummary' => false,
		'summary' => '',
		'showOnEmpty' => false,
		'emptyText' => '',
		'columns' => [
			[
				'attribute' => 'title',
				'visible' => $type->hasSummonVisibleField('title'),
			],
			[
				'attribute' => 'start_at',
				'format' => 'date',
				'visible' => $type->hasSummonVisibleField('start_at'),
				'label' => Yii::t('issue', 'Summon At'),
			],
			[
				'attribute' => 'deadline_at',
				'format' => 'date',
				'visible' => $type->hasSummonVisibleField('deadline_at'),
			],
			[
				'class' => ActionColumn::class,
				'controller' => '/issue/summon',
				'visibleButtons' => [
					'update' => static function (Summon $model): bool {
						return $model->isContractor(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
					},
					'delete' => static function (Summon $model): bool {
						return $model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
					},
				],
			],
		],
	])
	?>


<?php endforeach; ?>
