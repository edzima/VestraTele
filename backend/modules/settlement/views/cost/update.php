<?php

use backend\modules\settlement\models\IssueCostForm;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */

$this->title = Yii::t('backend', 'Update cost: {issue}', ['issue' => $model->getIssue()->longId]);
$this->params['breadcrumbs'][] = ['label' => 'Issue Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="issue-cost-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
