<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CauseCategory */

$this->title = Yii::t('frontend', 'Create Cause Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Cause Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
