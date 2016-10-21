<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ranking';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
				 'attribute' => 'tele',
				 'value' => 'tele.username',
				 'label' => 'Konsultant',
			],
			'suma'

        ],
    ]); ?>
	
	
	
    <?php Pjax::end(); ?>
</div>
