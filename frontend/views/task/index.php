<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\data\ActiveDataProvider;

use yii\helpers\ArrayHelper;
use common\models\AnswerTyp;
use  kartik\grid\GridView;

use common\models\AccidentTyp;


/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Umówione spotkania';

$this->params['breadcrumbs'][] = $this->title;


?>
<div class="task-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?=GridView::widget([
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
				['class' => 'kartik\grid\SerialColumn'],
				[
					'class' => 
					'\kartik\grid\DataColumn',
					'attribute' => 'id',
					'width' => '20px'
				],
				//'tele_id',
				[
					 'attribute' => 'agent',
					 'value' => 'agent.username',
					 'label' => 'Przedstawiciel',
				],

				'victim_name',
				'phone',
				// 'created_at',
				// 'updated_at',
				// 'accident_id',
				// 'woj',
				// 'powiat',
				// 'gmina',
				[
					 'attribute' => 'miasto',
					 'value' => 'miasto.name',
					 'label' => 'Miejscowość',
				],
				 'qualified_name',
				// 'details:ntext',
				[
					'class' => '\kartik\grid\BooleanColumn',
					'trueLabel' => 'Tak', 
					'falseLabel' => 'Nie',
					'attribute' => 'meeting',
					'showNullAsFalse' => true,
					'label' => 'Spotkanie'
				],
				 'date',
				['class' => 'kartik\grid\ActionColumn'],
			],
		'responsive'=>true,
		'pjax'=>true,
		'hover'=>true,
		'panel'=>[
				'type'=>GridView::TYPE_PRIMARY,
				'heading'=>'<i class="glyphicon glyphicon-road"></i>  Umówione spotkania',
				'footer'=>false,
		],
		'toolbar'=> [
			['content'=>
				Html::a('<i class="glyphicon glyphicon-plus"></i>Nowe', ['create'], ['class' => 'btn btn-success']).
				Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
			],
			'{toggleData}',
		],
	])
?>

<?= GridView::widget([
		'id' => 'kv-grid-demo',
		'dataProvider'=>$statusProvider,
		'columns'=>[
			[
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'id',
				'width' => '20px'
			],
		    [
				'class' => '\kartik\grid\BooleanColumn',
				'trueLabel' => 'Tak', 
				'falseLabel' => 'Nie',
				'attribute' => 'taskstatus',
				'value' => 'taskstatus.task_id',
				'showNullAsFalse' => true,
				'label' => 'Raport'
			],
			[
				'class' => '\kartik\grid\BooleanColumn',
				'trueLabel' => 'Tak', 
				'falseLabel' => 'Nie',
				'attribute' => 'finish',
				'value' => 'taskstatus.finished',
				'showNullAsFalse' => true,
				'label' => 'Zakończone'
			],
			[
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'taskstatus.count_agreement',
			],
			[	
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'victim_name',
			],
			[	
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'updated',
				'value' => 'taskstatus.updated_at',
				'label' => 'Zaraportowano'
			],
			
			[
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'answer',
				'value' => 'taskstatus.answer.name',
				'label' => 'Efekt',
				'filter' => ArrayHelper::map(AnswerTyp::find()->all(), 'id', 'name'),
		
			],
		],
		
		
		'filterModel'=>$searchStatus,
		//'columns'=>$gridColumns,
		'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
		'headerRowOptions'=>['class'=>'kartik-sheet-style'],
		'filterRowOptions'=>['class'=>'kartik-sheet-style'],
		'pjax'=>true, // pjax is set to always true for this demo
		// set your toolbar
		'toolbar'=> [
		['content'=>
			Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
		],
		'{toggleData}',
		],
		// set export properties
		'export'=>[
			'fontAwesome'=>true
			],
		'bordered'=>true,
		'striped'=>false,
		'condensed'=>true,
		'responsive'=>true,
		'hover'=>true,

		'panel'=>[
			'type'=>GridView::TYPE_PRIMARY,
			'heading'=>'<i class="glyphicon glyphicon-pencil"></i>  Stan raportów',
			'footer'=>false,
		],
		'persistResize'=>false,
		//'exportConfig'=>$exportConfig,
	])
?>
</div>
