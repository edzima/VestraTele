<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\CalculationForm;
use common\models\user\User;
use yii\web\View;

/* @var $this View */
/* @var $model CalculationForm */

$this->title = Yii::t('backend', 'Create administrative settlement for: {issue}', ['issue' => $model->getIssue()->getIssueName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());

if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->getIssue(), 'url' => ['issue', 'id' => $model->getIssue()->getIssueId()]];
}

$this->params['breadcrumbs'][] = Yii::t('backend', 'Create');

?>
<div class="settlement-calculation-create administrative">

	<?= $this->render('_issue-detail-view', [
		'model' => $model->getIssue(),
	]) ?>


	<?= $this->render('_form', [
		'model' => $model,
	]) ?>


</div>
