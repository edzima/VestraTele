<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

use yii\helpers\ArrayHelper;
use common\models\Task;
use common\models\City;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tasks';
$this->params['breadcrumbs'][] = $this->title;

//$dataCategory=ArrayHelper::map(City::find()->all(), 'id', 'name');

?>
<div class="task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'tele_id',
            'agent_id',
            'victim_name',
            'phone',
            // 'created_at',
            // 'updated_at',
            // 'accident_id',
            // 'woj',
            // 'powiat',
            // 'gmina',
            ['label'=>'Miasto', 'attribute'=>'cityName'],
			['label'=>'Author', 'value'=>function ($model, $index, $widget) { return $model->cityM->name; }],


             'qualified_name',
            // 'details:ntext',
            // 'meeting',
            // 'date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
