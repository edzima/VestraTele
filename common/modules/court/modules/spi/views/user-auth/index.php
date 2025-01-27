<?php

use common\modules\court\modules\spi\models\UserAuthSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var UserAuthSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('spi', 'SPI User Auths');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="spi-user-auth-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'label' => Yii::t('spi', 'User'),
				'filter' => $searchModel->getUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
						'placeholder' => Yii::t('spi', '...'),
					],
				],
			],
			'username',
			'last_action_at:datetime',
			'created_at:datetime',
			'updated_at:datetime',
			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>
