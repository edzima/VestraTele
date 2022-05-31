<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\issue\models\IssueClaimsForm;
use common\widgets\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $model IssueClaimsForm */

$this->title = Yii::t('issue', 'Create Claim: {issue}', [
	'issue' => $model->getIssue()->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Claims'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="create-multiple-issue-claims-form">


	<?= $this->render('_issue_view', [
		'model' => $model->getIssue(),
	]) ?>

	<?php $form = ActiveForm::begin(); ?>


	<div class="customer-claim">

		<fieldset>
			<legend><?= Yii::t('issue', 'Customer Claim') ?></legend>
			<?= $this->render('_form', [
				'model' => $model->getCustomer(),
				'form' => $form,
				'onlyField' => true,
			]) ?>
		</fieldset>

	</div>

	<div class="company-claim">
		<fieldset>
			<legend><?= Yii::t('issue', 'Company Claim') ?></legend>
			<?= $this->render('_form', [
				'model' => $model->getCompany(),
				'form' => $form,
				'onlyField' => true,
			]) ?>
		</fieldset>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
