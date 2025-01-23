<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\court\modules\spi\entity\NotificationDTO;
use common\modules\court\modules\spi\models\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\widgets\AppealsNavWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/** @var yii\web\View $this */
/** @var NotificationSearch $searchModel */
/** @var yii\data\DataProviderInterface $dataProvider */

$this->title = Module::t('notification', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="spi-notification-index">

	<?= AppealsNavWidget::widget() ?>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'type',
			'content',
			'date:datetime',
			'signature',
			'courtName',
			'read:boolean',
			[
				'class' => ActionColumn::class,
				'template' => '{read} {view} ',
				'urlCreator' => function ($action, $model, $key) use ($searchModel): string {
					return Url::to([$action, 'id' => $key, 'appeal' => $searchModel->getAppeal()]);
				},
				'buttons' => [
					'read' => function ($url, NotificationDTO $model) use ($searchModel): string {
						return Html::a(Html::icon('check'),
							[
								'read',
								'id' => $model->id,
								'signature' => $model->signature,
								'court' => $model->courtName,
								'appeal' => $searchModel->getAppeal(),
							],
						);
					},

				],
			],
		],
	]); ?>


</div>

