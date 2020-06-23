<?php

use backend\models\UserForm;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$this->title = Yii::t('backend', 'Nowy użytkownik');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

	<?php $form = ActiveForm::begin() ?>

	<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Activate')]) ?>

	<?= $form->field($model, 'parent_id')
		->widget(Select2::class, [
				'data' => $model->getParents(),
				'options' => [
					'placeholder' => 'Przełożony',
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
			]
		) ?>

	<?= $form->field($model, 'roles')->checkboxList($roles) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
	</div>


	<?php ActiveForm::end() ?>



	
