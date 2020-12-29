<?php

use common\widgets\GridView;
use frontend\models\search\SummonSearch;
use frontend\widgets\IssueColumn;
use kartik\grid\ActionColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SummonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Summons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => IssueColumn::class,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => SummonSearch::getTypesNames(),
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => SummonSearch::getStatusesNames(),
			],
			[
				'attribute' => 'term',
				'value' => 'termName',
				'filter' => SummonSearch::getTermsNames(),
			],
			'title',
			'start_at:date',
			'updated_at:datetime',
			'realized_at:datetime',
			'deadline:date',
			'owner',
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update}',
			],
		],
	]); ?>


</div>
