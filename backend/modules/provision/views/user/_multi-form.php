<?php

use backend\modules\provision\models\ProvisionUserForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserForm */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="provision-user-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $this->render('_form_models', [
		'title' => 'Swoje',
		'form' => $form,
		'formModel' => $model,
		'models' => $model->getSelfModels(),
	]) ?>

	<?= $this->render('_form_models', [
		'title' => 'Przełożeni',
		'form' => $form,
		'formModel' => $model,
		'models' => $model->getParentsModels(),
	]) ?>

	<?= $this->render('_form_models', [
		'title' => 'Podwładni',
		'form' => $form,
		'formModel' => $model,
		'models' => $model->getChildesModels(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>


	<?php ActiveForm::end(); ?>

</div>



