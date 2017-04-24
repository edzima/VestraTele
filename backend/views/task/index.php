<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\models\AnswerTyp;
use common\models\AccidentTyp;
use common\models\User;

use  kartik\grid\GridView;



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
				[
					 'attribute' => 'tele',
					 'value' => 'tele.username',
					 'label' => 'Konsultant',
					'filter' => ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username')
				],
				[
					 'attribute' => 'agent',
					 'value' => 'agent.username',
					 'label' => 'Przedstawiciel',
					 'filter' => ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username')
				],
				'victim_name',
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
					'label' => 'Umówione?'
				],
				[
					'class' => '\kartik\grid\BooleanColumn',
					'trueLabel' => 'Tak',
					'falseLabel' => 'Nie',
					'attribute' => 'automat',
					'showNullAsFalse' => true,
					'label' => 'Automat'
				],
                [
                      'attribute' => 'date',
                      'format' => ['date']
                  ],
				['class' => 'kartik\grid\ActionColumn'],
			],
		'responsive'=>true,
		'pjax'=>true,
		'hover'=>true,
		'panel'=>[
				'type'=>GridView::TYPE_PRIMARY,
				'heading'=>'<i class="glyphicon glyphicon-road"></i>  Umówione spotkania',
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
</div>
