<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
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

    <?= $form->field($model, 'roles')->checkboxList($roles) ?>
	
	
	<?= $form->field($model, 'typ_work')->dropDownList(['prompt'=> 'Wybierz typ pracownika', 'T'=>'Telemarketer', 'P'=>'Przedstawiciel']) ?>															

	
    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
    </div>
	

    <?php ActiveForm::end() ?>	

	<?php
	$this->registerJs(
		'$("document").ready(function(){
			
			$(".field-userform-typ_work").toggle();
			
				
			$("#userform-roles").on("change",function(event){
				console.log(event.target.defaultValue);
				var role = event.target.defaultValue;
				
				if (role =="user")  $(".field-userform-typ_work").toggle();
			});

			
		});'		
	);

?>

	
