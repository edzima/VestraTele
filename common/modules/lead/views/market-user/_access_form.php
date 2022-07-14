<?php

use common\modules\lead\models\forms\LeadMarketAccessRequest;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketAccessRequest */
/* @var $form ActiveForm */

?>

<div class="lead-market-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-market-access-request-form',
	]); ?>

	<div class="row">


		<div class="col-md-5 col-lg-4">

			<?= $form->field($model, 'days')->widget(NumberControl::class, [
				'maskedInputOptions' => [
					'digits' => 0,
				],
			]) ?>

			<?= $form->field($model, 'details')->textarea() ?>

		</div>


	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Request Access'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
