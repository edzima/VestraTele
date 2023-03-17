<?php

use common\models\issue\IssueTagType;

/* @var $this yii\web\View */
/* @var $model IssueTagType */

$this->title = Yii::t('backend', 'Create Issue Tag Type');
$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = ['url' => ['tag/index'], 'label' => Yii::t('issue', 'Tags')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Tag Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-tag-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
