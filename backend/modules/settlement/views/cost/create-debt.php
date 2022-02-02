<?php

use backend\modules\settlement\models\DebtCostsForm;

/* @var $this \yii\web\View */
/* @var $model DebtCostsForm */

$this->title = Yii::t('settlement', 'Create Debt Costs: {issue}', ['issue' => $model->getIssue()->longId]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->longId, 'url' => ['/issue/issue/view', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Costs'), 'url' => ['issue', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = Yii::t('settlement', 'Create Debt Costs');
?>

<div class="cost-create-debt">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
