<?php

use common\models\hint\HintCity;
use common\models\hint\searches\HintCitySourceSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel HintCitySourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('hint', 'Hint City Sources');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['city/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hint-city-source-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'source_id',
				'value' => 'source.name',
				'filter' => $searchModel::getSourcesNames(),
			],
			[
				'attribute' => 'rating',
				'value' => 'ratingName',

				'filter' => HintCitySourceSearch::getRatingsNames(),
			],
			'phone',
			[
				'attribute' => 'hintType',
				'value' => 'hint.typeName',
				'filter' => HintCity::getTypesNames(),
				'label' => Yii::t('hint', 'Type'),
			],
			[
				'attribute' => 'hintStatus',
				'value' => 'hint.statusName',
				'filter' => HintCity::getStatusesNames(),
				'label' => Yii::t('hint', 'Status'),

			],
			[
				'attribute' => 'hintCityName',
				'value' => 'hint.city.name',
				'label' => Yii::t('common', 'City'),
			],

			'details:ntext',
			'created_at:date',
			'updated_at:date',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
