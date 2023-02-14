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

<div class="summon-docs-widget">
	<fieldset>
		<legend>
			<?= Yii::t('issue', 'Summon Docs') ?>
		</legend>

		<div class="row">
			<div class="col-md-4">
				<?= GridView::widget([
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
			</div>

			<div class="col-md-4">
				<?= GridView::widget([
					'caption' => Yii::t('issue', 'To Confirm'),
					'dataProvider' => $toConfirmDataProvider,
					'columns' => [
						'doc.name',
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
			</div>

			<div class="col-md-4">
				<?= GridView::widget([
					'caption' => Yii::t('issue', 'Confirmed'),
					'dataProvider' => $confirmedDataProvider,
					'columns' => [
						'doc.name',
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
		</div>
	</fieldset>
</div>
