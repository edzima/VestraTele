<?php

use backend\modules\issue\models\IssueUserForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueUserForm */

$this->title = Yii::t('backend', 'Link {user} to issue', ['user' => $model->getUser()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-user-link">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="issue-type-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'issue_id')->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'type')->dropDownList(IssueUserForm::getTypesNames()) ?>


		<div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>
