<?php

use common\helpers\Url;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\Module;
use kartik\tabs\TabsX;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var LawsuitViewIntegratorDto $model */

?>
<div class="court-lawsuit-spi-details-view">

	<?= TabsX::widget([
		'items' => [
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
						'description:ntext',
						'receiptDate:datetime',
						[
							'attribute' => 'finishDate',
							'format' => 'date',
							'visible' => !empty($model->finishDate),
						],

						'departmentName',
						'judgeName',
						'value',
					],
				]),
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
			[
				'label' => Module::t('document', 'Posiedzenia'),
				'linkOptions' => [
					'data-url' => Url::to([
						'spi/lawsuit/sessions',
						'id' => $model->id,
						'appeal' => $this->params['appeal'],
					]),
				],
			],
		],
	]) ?>

</div>
