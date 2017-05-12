<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CauseCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Cause Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Cause Category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'period',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
