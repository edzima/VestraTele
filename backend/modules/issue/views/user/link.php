<?php

use backend\modules\issue\models\IssueUserForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueUserForm */

$this->title = Yii::t('backend', 'Link {user} to issue', ['user' => $model->getUser()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issue Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-user-link">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="issue-type-form">

		<?php $form = ActiveForm::begin(['id' => 'issue-user-form']); ?>
		<div class="row">
			<?= $form->field($model, 'issue_id', [
				'options' => [
					'class' => 'col-md-1',
				],
			])->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'type', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->dropDownList(IssueUserForm::getTypesNames()) ?>
		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>
