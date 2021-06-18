<?php

use common\modules\lead\models\Lead;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Lead */

$this->title = Yii::t('lead', 'Create Lead');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
