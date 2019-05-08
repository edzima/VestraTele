<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueStage */

$this->title = 'Dodaj etap';
$this->params['breadcrumbs'][] = ['label' => 'Issue Stages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-stage-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
