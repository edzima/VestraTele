<?php

use common\modules\court\modules\spi\models\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\widgets\AppealsNavWidget;
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
		],
	]); ?>


</div>

