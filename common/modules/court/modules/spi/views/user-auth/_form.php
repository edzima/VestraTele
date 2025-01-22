<?php

use common\helpers\Html;
use common\modules\court\modules\spi\models\auth\SpiUserAuth;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var SpiUserAuth $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="spi-user-auth-form">


	<?php $form = ActiveForm::begin(); ?>

	<div class="row">


		<?= $form->field($model, 'username', [
			'options' => [
				'class' => [
					'col-sm-12 col-md-4 col-lg-3',
				],
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'password', [
			'options' => [
				'class' => [
					'col-sm-12 col-md-4 col-lg-3',
				],
			],
		])->passwordInput(['maxlength' => true]) ?>

	</div>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('spi', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
