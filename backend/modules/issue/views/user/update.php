<?php

use backend\modules\issue\models\IssueUserForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueUserForm */

$this->title = Yii::t('backend', 'Update relation in {issue} for: {user}', [
	'issue' => $model->getIssue(),
	'user' => $model->getUser(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-user-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="issue-type-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'type')->dropDownList(IssueUserForm::getTypesNames()) ?>

		<div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>
