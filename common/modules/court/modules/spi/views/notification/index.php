<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\court\modules\spi\entity\notification\NotificationDTO;
use common\modules\court\modules\spi\entity\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\widgets\AppealsNavWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/** @var yii\web\View $this */
/** @var NotificationSearch $searchModel */
/** @var yii\data\DataProviderInterface $dataProvider */
/** @var int $unreadCount */

$this->title = Module::t('notification', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="spi-notification-index">

	<?= $unreadCount
		? Html::a(
			Module::t('notification', 'Read all'),
			['read-all', 'appeal' => $searchModel->getAppeal()],
			[
				'data-method' => 'post',
				'class' => 'btn btn-warning pull-right',
			],
		)
		: '' ?>

	<?= AppealsNavWidget::widget([
		'withUnreadCount' => true,
		'activeAppeal' => $searchModel->getAppeal(),
	]) ?>



	<?= $this->render('_search', ['model' => $searchModel]); ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'rowOptions' => static function (NotificationDTO $data) use ($searchModel): array {
			if ($data->read) {
				return [
					'class' => 'half-transparent',
				];
			}
			return [
				'style' => [
					'background-color' => '#f5c6cb',
				],
			];
		},
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
				'urlCreator' => function ($action, NotificationDTO $model, $key) use ($searchModel): string {
					return Url::to([
						$action,
						'id' => $model->id,
						'appeal' => $searchModel->getAppeal(),
						'signature' => $model->signature,
						'court' => $model->courtName,
					]);
				},
				'visibleButtons' => [
					'read' => function (NotificationDTO $model): bool {
						return !$model->read;
					},
				],
				'buttons' => [
					'read' => function ($url, NotificationDTO $model) use ($searchModel): string {
						return Html::a(Html::icon('check'), $url, [
							'data-method' => 'POST',
						]);
					},

				],
			],
		],
	]); ?>


</div>

