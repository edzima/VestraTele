<?php

use common\helpers\Url;
use common\models\user\User;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\Module;
use kartik\tabs\TabsX;
use yii\data\DataProviderInterface;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var LawsuitViewIntegratorDto $model */
/** @var DataProviderInterface $parties |null */

$items = [

	[
		'label' => Module::t('lawsuit', 'Lawsuit'),
		'content' => DetailView::widget([
			'model' => $model,
			'attributes' => [
				[
					'attribute' => 'result',
					'visible' => !empty($model->result),
				],
				'subject',
				[
					'attribute' => 'description',
					'format' => 'ntext',
					'visible' => !empty($model->description),
				],
				'receiptDate:datetime',
				[
					'attribute' => 'finishDate',
					'format' => 'date',
					'visible' => !empty($model->finishDate),
				],

				'departmentName',
				'judgeName',
				[
					'attribute' => 'value',
					'visible' => !empty($model->value),
				],
			],
		]),
	],
	[
		'label' => Module::t('lawsuit', 'Parties'),
		'linkOptions' => [
			'data-url' => Url::to([
				'spi/lawsuit/parties',
				'id' => $model->id,
				'appeal' => $this->params['appeal'],
			]),
		],
		//				'content' => GridView::widget([
		//					'dataProvider' => new ArrayDataProvider([
		//						'allModels' => $model->getLawsuitParties(),
		//						'modelClass' => LawsuitPartyDTO::class,
		//					]),
		//				]),
	],
	[
		'label' => Module::t('document', 'Documents'),
		'linkOptions' => [
			'data-url' => Url::to([
				'spi/document/lawsuit',
				'id' => $model->id,
				'appeal' => $this->params['appeal'],
			]),
		],
	],
	[
		'label' => Module::t('lawsuit', 'Proceedings'),
		'linkOptions' => [
			'data-url' => Url::to([
				'spi/lawsuit/proceedings',
				'id' => $model->id,
				'appeal' => $this->params['appeal'],
			]),
		],
	],
];
if (Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
	$items[] = [
		'label' => Module::t('lawsuit', 'Sessions'),
		'linkOptions' => [
			'data-url' => Url::to([
				'spi/lawsuit/sessions',
				'id' => $model->id,
				'appeal' => $this->params['appeal'],
			]),
		],
	];
}
?>
<div class="court-lawsuit-spi-details-view">

	<?= TabsX::widget([
		'items' => $items,
	]) ?>

</div>
