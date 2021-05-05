<?php

use common\models\hint\searches\HintCitySearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel HintCitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('hint', 'Hint Cities');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hint-city-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('hint', 'Create Hint City'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('hint', 'Create Hint District'), ['create-district'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => $searchModel::getTypesNames(),
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => $searchModel::getStatusesNames(),
			], [
				'attribute' => 'user_id',
				'value' => 'user.fullName',
				'filter' => $searchModel::getUsersNames(),
			],
			[
				'attribute' => 'cityName',
				'value' => 'city.name',
				'label' => Yii::t('common', 'City'),
			],
			'details:ntext',
			'created_at:date',
			'updated_at:date',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
