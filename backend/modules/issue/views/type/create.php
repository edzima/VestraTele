<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueType */

$this->title = 'Dodaj rodzaj';
$this->params['breadcrumbs'][] = ['label' => 'Rodzaje', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
