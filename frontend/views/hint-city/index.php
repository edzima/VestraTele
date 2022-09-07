<?php

use common\models\hint\HintCity;
use common\widgets\grid\ActionColumn;
use frontend\helpers\Html;
use frontend\models\search\HintCitySearch;
use frontend\widgets\GridView;
use yii\data\DataProviderInterface;

/* @var $this \yii\web\View */
/* @var $searchModel HintCitySearch */
/* @var $dataProvider DataProviderInterface */

$this->title = Yii::t('hint', 'Hint Cities');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>


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
		],
		[
			'attribute' => 'cityName',
			'value' => 'city.name',
			'label' => Yii::t('common', 'City'),
		],

		'details:ntext',
		[
			'class' => ActionColumn::class,
			'template' => '{view} {update} {create-source}',
			'buttons' => [
				'create-source' => static function ($url, HintCity $model): string {
					return Html::a(Html::icon('plus'), ['hint-city-source/create', 'id' => $model->id]);
				},
			],
		],
	],
]); ?>
