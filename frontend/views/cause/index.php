<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CauseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Causes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Cause'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'victim_name',
            [
                'attribute' => 'author',
                'value' => 'author.username',
                'label' => 'Autor',
            ],
            [
                'attribute' => 'category',
                'value' => 'category.name',
                'label' => 'Etap',
            ],
            'created_at:datetime',
            'updated_at:datetime',
            'date:datetime',

            // 'category_id',
             'is_finished:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
