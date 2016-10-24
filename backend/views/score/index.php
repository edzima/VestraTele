<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\AnswerTyp;

use  kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Punktacja';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	
	<?php     
	echo GridView::widget([
		'id' => 'kv-grid-demo',
		'dataProvider'=>$dataProvider,
		'columns'=>[
			[	
				
			   'class' => 'yii\grid\ActionColumn',
			    'template' => '{see}{points}',
				'buttons' => [
					  'see' => function ($url, $model, $key) {
							$options = [
								'title' => 'Podgląd',
								'aria-label' => 'Podgląd',
								'data-pjax' => '0',
							];
							$url = \yii\helpers\Url::toRoute(['task-status/raport', 'id' => $key]);

							return Html::a('<button type="button" class="btn btn-default">
												<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
											</button>', 
											$url, $options);
						},
						'points' => function ($url, $model, $key) {
							$options = [
								'title' => 'Rozdaj',
								'aria-label' => 'Rozdaj',
								'data-pjax' => '0',
							];
							$url = \yii\helpers\Url::toRoute(['score/deal', ['id' => $key, 'tele' => $model->tele_id]]);

							return Html::a('<button type="button" class="btn btn-default">
												<span class="glyphicon glyphicon-tower" aria-hidden="true"></span>
											</button>', $url, $options);
						}
				],
			],
			'id',
			[
				'class' => '\kartik\grid\BooleanColumn',
				'trueLabel' => 'Tak', 
				'falseLabel' => 'Nie',
				'attribute' => 'finished',
				'value' => 'taskstatus.finished',
				'showNullAsFalse' => true,
				'label' => 'Zakończone'
			],
		    [
				'class' => '\kartik\grid\BooleanColumn',
				'trueLabel' => 'Tak', 
				'falseLabel' => 'Nie',
				'attribute' => 'status',
				'value' => 'taskstatus.point',
				'label' => 'Już rozdane'
			],
			[	 'class' => '\kartik\grid\DataColumn',
				 'attribute' => 'tele',
				 'value' => 'tele.username',
				 'label' => 'Konsultant',
			],
			[
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'Umowy',
				'value' => 'taskstatus.count_agreement',
			],
			[	
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'victim_name',
			],
			'date',
			[
				'class' => 
				'\kartik\grid\DataColumn',
				'attribute' => 'answer',
				'value' => 'taskstatus.answer.name',
				'label' => 'Efekt',
				'filter' => ArrayHelper::map(AnswerTyp::find()->all(), 'id', 'name')
		
			],
			[
				'class' => 
				'\kartik\grid\CheckboxColumn',
				'contentOptions' => ['class' => 'kv-row-select'],
				'headerOptions' => ['class' => 'kv-all-select'],
			],
		],
		
		
		'filterModel'=>$searchModel,
		//'columns'=>$gridColumns,
		'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
		'headerRowOptions'=>['class'=>'kartik-sheet-style'],
		'filterRowOptions'=>['class'=>'kartik-sheet-style'],
		'pjax'=>true, // pjax is set to always true for this demo
		// set your toolbar
		'toolbar'=> [
		['content'=>
			Html::button('<i class="glyphicon glyphicon-plus"></i>', ['type'=>'button', 'title'=>Yii::t('kvgrid', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
			Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
		],
	
		'{toggleData}',
		],
	
		'bordered'=>true,
		'striped'=>false,
		'condensed'=>true,
		'responsive'=>true,
		'hover'=>true,

		'panel'=>[
			'type'=>GridView::TYPE_PRIMARY,
			'heading'=>'<i class="glyphicon glyphicon-book"></i>Punktacja'
		],
		'persistResize'=>false,

	]);
?>
	
	
	
    <?php Pjax::end(); ?>
</div>
