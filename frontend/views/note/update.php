<?php

use backend\modules\issue\models\IssueNoteForm;
use frontend\helpers\Breadcrumbs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
$this->title = Yii::t('issue', 'Update Issue Note: {title}', ['title' => $model->title]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getModel());
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
