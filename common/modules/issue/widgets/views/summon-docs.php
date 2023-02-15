<?php

use common\models\issue\SummonDocLink;
use common\modules\issue\widgets\SummonDocsLinkActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;

/** @var string $controller */
/** @var string $returnUrl */
/** @var DataProviderInterface $toDoDataProvider */
/** @var DataProviderInterface $toConfirmDataProvider */
/** @var DataProviderInterface $confirmedDataProvider */

?>
<?php if ($toConfirmDataProvider->getTotalCount() || $toDoDataProvider->getTotalCount() || $confirmedDataProvider->getTotalCount()): ?>
	<div class="summon-docs-widget">
		<fieldset>
			<legend>
				<?= Yii::t('issue', 'Summon Docs') ?>
			</legend>

			<div class="row">
				<?= GridView::widget([
					'containerOptions' => [
						'class' => 'col-md-2',
					],
					'caption' => Yii::t('issue', 'To Do'),
					'dataProvider' => $toDoDataProvider,
					'columns' => [
						'doc.name',
						[
							'class' => SummonDocsLinkActionColumn::class,
							'status' => SummonDocLink::STATUS_TO_DO,
							'controller' => $controller,
							'returnUrl' => $returnUrl,
						],
					],
					'emptyText' => false,
					'showOnEmpty' => false,
					'summary' => '',
				]) ?>

				<?= GridView::widget([
					'containerOptions' => [
						'class' => 'col-md-4',
					],
					'caption' => Yii::t('issue', 'To Confirm'),
					'dataProvider' => $toConfirmDataProvider,
					'columns' => [
						'doc.name',
						[
							'attribute' => 'doneUser',
							'value' => function (SummonDocLink $docLink): ?string {
								return $docLink->userNameWithDate($docLink->doneUser, $docLink->done_at);
							},
							'format' => 'html',
						],
						[
							'class' => SummonDocsLinkActionColumn::class,
							'status' => SummonDocLink::STATUS_TO_CONFIRM,
							'controller' => $controller,
							'returnUrl' => $returnUrl,
						],
					],
					'emptyText' => false,
					'showOnEmpty' => false,
					'summary' => '',
				]) ?>

				<?= GridView::widget([
					'containerOptions' => [
						'class' => 'col-md-5',
					],
					'caption' => Yii::t('issue', 'Confirmed'),
					'dataProvider' => $confirmedDataProvider,
					'columns' => [
						'doc.name',
						[
							'attribute' => 'doneUser',
							'value' => function (SummonDocLink $docLink): ?string {
								return $docLink->userNameWithDate($docLink->doneUser, $docLink->done_at);
							},
							'format' => 'html',
						],
						[
							'attribute' => 'confirmedUser',
							'value' => function (SummonDocLink $docLink): ?string {
								return $docLink->userNameWithDate($docLink->confirmedUser, $docLink->confirmed_at);
							},
							'format' => 'html',

						],
						[
							'class' => SummonDocsLinkActionColumn::class,
							'status' => SummonDocLink::STATUS_CONFIRMED,
							'controller' => $controller,
							'returnUrl' => $returnUrl,
						],
					],
					'emptyText' => false,
					'showOnEmpty' => false,
					'summary' => '',
				]) ?>
			</div>
		</fieldset>
	</div>
<?php endif; ?>
