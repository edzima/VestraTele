<?php

use common\models\hint\HintCity;
use common\models\hint\HintCitySource;
use common\models\hint\searches\HintCitySourceSearch;
use common\widgets\grid\ActionColumn;
use frontend\helpers\Html;
use frontend\widgets\GridView;
use yii\data\DataProviderInterface;

/* @var $this \yii\web\View */
/* @var $searchModel HintCitySourceSearch */
/* @var $dataProvider DataProviderInterface */

$this->title = Yii::t('hint', 'Hint Sources');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>


<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		[
			'attribute' => 'source_id',
			'value' => 'source.name',
			'filter' => HintCitySourceSearch::getSourcesNames(),
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
		[
			'class' => ActionColumn::class,
			'template' => '{view} {update} {delete}',
			'buttons' => [

				'view' => static function ($url, HintCitySource $model): string {
					return Html::a(Html::icon('eye-open'), ['hint-city/view', 'id' => $model->hint_id]);
				},
			],
		],
	],
]); ?>
