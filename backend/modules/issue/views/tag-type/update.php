<?php

use common\models\issue\IssueTagType;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueTagType */

$this->title = Yii::t('backend', 'Update Issue Tag Type: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = ['url' => ['tag/index'], 'label' => Yii::t('issue', 'Tags')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Tag Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-tag-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
