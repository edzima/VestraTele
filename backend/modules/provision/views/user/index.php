<?php

use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ProvisionUserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Prowizje pracowników';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-user-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'fromUsername',
				'format' => 'raw',
				'value' => static function (ProvisionUser $data) {
					return Html::a($data->fromUser, ['user', 'id' => $data->from_user_id,], ['target' => '_blank']);
				},
			],
			[
				'attribute' => 'toUsername',
				'format' => 'raw',
				'value' => static function (ProvisionUser $data) {
					return Html::a($data->toUser, ['user', 'id' => $data->to_user_id,], ['target' => '_blank']);
				},
			],
			[
				'attribute' => 'type_id',
				'value' => 'type',
				'filter' => ProvisionUserSearch::getTypesNames(),
			],
			[
				'attribute' => 'value',
				'value' => 'formattedValue',
			],
			'isDefaultValue:boolean',
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{delete}',
			],
		],
	]); ?>


</div>
