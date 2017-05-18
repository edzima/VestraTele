<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CauseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Causes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [


            'id',
            'victim_name',
            [
                'attribute' => 'category',
                'value' => 'category.name',
                'label' => 'Etap',
                'filter' => ArrayHelper::map(\common\models\CauseCategory::find()->all(), 'id', 'name')
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'date:datetime',

            // 'category_id',
            [
                'attribute' => 'is_finished',
                'format' => 'boolean',
                'filter' => [1=>'Tak', 0=>'Nie']
            ],
//             /'is_finished:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
