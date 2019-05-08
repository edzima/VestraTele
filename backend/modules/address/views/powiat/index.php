<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PowiatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Powiaty';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="powiat-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
				'attribute'=>'wojewodztwo',
				'value' => 'wojewodztwo.name',
				'label' =>'Województwo'
			],
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
