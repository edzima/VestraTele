<?php

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model CustomerUserForm */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Create customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-create">

	<?php $form = ActiveForm::begin() ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<?= UserProfileFormWidget::widget([
		'model' => $model->getProfile(),
		'form' => $form,
	]) ?>


	<?= AddressFormWidget::widget([
		'form' => $form,
		'model' => $model->getAddress(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
