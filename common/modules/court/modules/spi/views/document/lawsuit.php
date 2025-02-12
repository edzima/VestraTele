<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\court\modules\spi\entity\document\DocumentInnerViewDto;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\View;

/** @var View */
/** @var DataProviderInterface $dataProvider */

?>

<div class="documents-lawsuit">
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'autoXlFormat' => true,
		'summary' => false,
		'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'refreshGrid' => true,
		],
		'columns' => [
			'documentName',
			'createdDate:datetime',
			'modificationDate:datetime',
			'downloaded:boolean',
			[
				'class' => ActionColumn::class,
				'template' => '{download} {pdf}',
				'urlCreator' => function ($action, DocumentInnerViewDto $model): string {
					return Url::toRoute([
						$action,
						'id' => $model->id,
						'fileName' => $model->fileName,
						'appeal' => $this->params['appeal'],
					]);
				},
				'buttons' => [
					'download' => function ($url): string {
						return
							Html::a(
								Html::icon('download'),
								$url,
							);
					},
					'pdf' => function ($url): string {
						return Html::a(
							Html::icon('eye-open'),
							$url, [
								'target' => '_blank',
							]
						);
					},
				],
			],
		],
	]) ?>
</div>
