<?php

use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ranking';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	
	<?=GridView::widget([
		'dataProvider'=> $dataProvider,
		'columns' => [
				['class' => 'kartik\grid\SerialColumn'],
				[
					'class' => 
					'\kartik\grid\DataColumn',
					'attribute' => 'tele',
					'value' => 'tele.username',
					'label' => 'Konsultant',
				],
				[
					'class' => 
					'\kartik\grid\DataColumn',
					'attribute' => 'suma',
					'label' => 'Punkty'
				],
			],
		'responsive'=>true,
		'pjax'=>true,
		'hover'=>true,
		'panel'=>[
				'type'=>GridView::TYPE_PRIMARY,
				'heading'=>'<i class="glyphicon glyphicon-tower"></i>  Ranking',
				'footer'=>false,
		],
		'toolbar'=> false,
	])
?>
	
</div>
