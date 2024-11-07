<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\IssueCostForm;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */

$this->title = Yii::t('settlement', 'Update Issue Cost');
if ($model->getIssue()) {
	$this->title = Yii::t('settlement', 'Update Issue Cost: {issue}', ['issue' => $model->getIssue()->getIssueName()]);
	$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeNameWithId(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="issue-cost-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
