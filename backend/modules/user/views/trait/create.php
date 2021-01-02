<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserTrait */

$this->title = Yii::t('backend', 'Create User Trait');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Traits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-trait-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
