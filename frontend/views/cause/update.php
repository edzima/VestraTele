<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cause */

$this->title = Yii::t('frontend', 'Sprawa nr : ') . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Causes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="cause-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class=" col-lg-5 col-md-5">
        <?= $this->render('_updateForm', [
            'model' => $model,
            'category' => $category
        ]) ?>
    </div>


    <div class="comment col-lg-7 col-md-7"

        <?php echo \yii2mod\comments\widgets\Comment::widget([
            'model' => $model,
        ]); ?>

    </div>


</div>
