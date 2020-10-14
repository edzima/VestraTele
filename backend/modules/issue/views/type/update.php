<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueType */

$this->title = 'Edytuj rodzaj: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="issue-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
