<?php

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\SummonDocLink;
use common\modules\issue\widgets\SummonDocsLinkActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\View;

/** @var $this View */
/** @var string $controller */
/** @var string $returnUrl */
/** @var DataProviderInterface $dataProvider */
/** @var DataProviderInterface $toDoDataProvider */
/** @var DataProviderInterface $toConfirmDataProvider */
/** @var DataProviderInterface $confirmedDataProvider */

?>

<?= GridView::widget([
	'caption' => Yii::t('issue', 'Summon Docs'),
	'id' => 'docs-grid',
	'rowOptions' => function (SummonDocLink $docLink): array {
		if ($docLink->isConfirmed()) {
			return [
				'class' => 'success',
			];
		}
		if ($docLink->isToConfirm()) {
			return [
				'class' => 'warning',
			];
		}
		return [
			'class' => 'danger',
		];
	},
	'summary' => '',
	'emptyText' => false,
	'showOnEmpty' => false,
	'dataProvider' => $dataProvider,
	'columns' => [
		[
			'attribute' => 'doc.name',
			'contentOptions' => [
				'class' => 'text-uppercase',
			],
		],
		[
			'attribute' => 'done_at',
			'contentCenter' => true,
			'format' => 'raw',
			'headerOptions' => [
				'class' => 'text-center',
			],
			'tooltip' => true,
			'value' => function (SummonDocLink $docLink): string {
				if (empty($docLink->done_at)) {
					return Html::icon('remove-sign', [
						'class' => 'text-danger',
					]);
				}
				return Html::tag('span',
					Html::icon('ok-sign'), [
						'class' => 'text-success',
						TooltipAsset::DEFAULT_ATTRIBUTE_NAME =>
							Html::encode($docLink->doneUser->getFullName()) .
							' - ' . Yii::$app->formatter->asDate($docLink->done_at),
					]);
			},
		],
		[
			'attribute' => 'confirmed_at',
			'contentCenter' => true,
			'format' => 'raw',
			'headerOptions' => [
				'class' => 'text-center',
			],
			'tooltip' => true,
			'value' => function (SummonDocLink $docLink): string {
				if (empty($docLink->confirmed_at)) {
					return Html::icon('remove-sign', [
						'class' => 'text-danger',
					]);
				}
				return Html::tag('span',
					Html::icon('ok-sign'), [
						'class' => 'text-success',
						TooltipAsset::DEFAULT_ATTRIBUTE_NAME =>
							Html::encode($docLink->confirmedUser->getFullName()) .
							' - ' . Yii::$app->formatter->asDate($docLink->confirmed_at),
					]);
			},
		],
		[
			'class' => SummonDocsLinkActionColumn::class,
			'controller' => $controller,
			'returnUrl' => $returnUrl,
		],
	],
]) ?>
