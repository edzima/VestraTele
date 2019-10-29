<?php

use backend\modules\issue\models\IssueForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueForm */
$issue = $model->getModel();
$this->title = 'Edytuj: ' . $issue->longId;
$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $issue, 'url' => ['view', 'id' => $issue->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="issue-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $issue,
		'payAddress' => $model->getPayAddress(),
	]) ?>

</div>
