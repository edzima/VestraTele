<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Miejscowości';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-index">

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
            'name',
			[
				'attribute'=>'wojewodztwo',
				'value'=>'wojewodztwo.name',
				'label' => 'Województwo'
			],
			[
				'attribute'=>'powiat',
				'value'=>'powiatRel.name',
				'label' => 'Powiat'
			],
 

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
