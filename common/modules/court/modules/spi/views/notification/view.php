<?php

use common\helpers\Html;
use common\modules\court\modules\spi\entity\lawsuit\NotificationLawsuit;
use common\modules\court\modules\spi\entity\notification\NotificationViewDTO;
use common\modules\court\modules\spi\Module;
use yii\web\View;
use yii\widgets\DetailView;

/** @var View $this */
/** @var NotificationViewDTO $model */
/** @var string $appeal */

$this->title = Module::t('notification', 'Notification: {type}', [
	'type' => $model->type,
]);
$this->params['breadcrumbs'][] = [
	'url' => ['index'],
	'label' => Module::t('notification', 'Notifications'),
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="spi-notification-view">

	<h3>
		<?= Html::encode($model->content) ?>
	</h3>

	<div class="row">
		<div class="col-sm-12 col-md-6 col-lg-5">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					//	'content:ntext',
					'date:date',
					'read:boolean',
				],
			])
			?>

			<?= DetailView::widget([
				'model' => $model->getLawsuit(),
				'attributes' => [
					[
						'attribute' => 'signature',
						'value' => function (NotificationLawsuit $lawsuit) use ($appeal): string {
							return Html::a($lawsuit->signature, [
								'lawsuit/view',
								'id' => $lawsuit->id,
								'appeal' => $appeal,
							]);
						},
						'format' => 'html',
					],
					'courtName',
					'visible:boolean',
				],
			])
			?>
		</div>

	</div>


</div>



