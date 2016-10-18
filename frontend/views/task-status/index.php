<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Umówione spotkania';
$this->params['breadcrumbs'][] = $this->title;

use yii\data\SqlDataProvider;
use yii\data\ActiveDataProvider;

use common\models\TaskStatus;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$dataProvider2 = new SqlDataProvider([
    'sql' => 'SELECT * FROM task LEFT JOIN task_status on id=task_id',
   
]);


?>
<div class="task-status-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Task Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider2,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			
            'task_id',
			'id',
			
            'answer_id',
            'count_agreement',
            'status_details:ntext',
            'name',
			
			[
             'label'=>'bla',
             'format' => 'raw',
             'value'=>function ($data) {
                        return Html::a('link_text','site/index');
                      },
             ],

            ['class' => 'yii\grid\ActionColumn'],

			
        ],
    ]); ?>
	
	
	<?= 
ListView::widget([
    'dataProvider' => $dataProvider2,
]); 
?>
</div>
