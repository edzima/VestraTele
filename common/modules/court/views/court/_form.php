<?php

use common\modules\court\models\Court;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Court $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="court-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'phone')->textarea(['rows' => 2]) ?>

	<?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
