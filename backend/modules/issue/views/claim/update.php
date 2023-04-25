<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueClaimForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueClaimForm */

$this->title = Yii::t('issue', 'Update Issue Claim: {name}', [
	'name' => $model->getModel()->getTypeWithEntityName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Claims'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-claim-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'onlyField' => false,
	]) ?>

</div>
