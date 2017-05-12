<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Cause */

$this->title = Yii::t('frontend', 'Create Cause');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Causes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'category' => $category,
    ]) ?>

</div>
