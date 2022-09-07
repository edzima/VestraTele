<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueRelation */

$this->title = Yii::t('issue', 'Update Issue Relation: {name}', [
	'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('issue', 'Update');
?>
<div class="issue-relation-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
