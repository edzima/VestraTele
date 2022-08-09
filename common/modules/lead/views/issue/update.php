<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadIssue */

$this->title = Yii::t('lead', 'Update Lead Issue: {name}', [
	'name' => $model->lead_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lead_id, 'url' => ['view', 'lead_id' => $model->lead_id, 'issue_id' => $model->issue_id, 'crm_id' => $model->crm_id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-issue-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
