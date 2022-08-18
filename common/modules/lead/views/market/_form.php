<?php

use common\modules\lead\models\forms\LeadMarketForm;
use common\modules\lead\models\forms\LeadMarketMultipleForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketForm|LeadMarketMultipleForm */
/* @var $form ActiveForm */

?>

<div class="lead-market-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-market-form',
	]); ?>

	<div class="row">


		<div class="col-md-6">

			<?= $model instanceof LeadMarketMultipleForm ?
				Html::hiddenInput('leadsIds', implode(',', $model->leadsIds))
				: ''
			?>


			<?= $model instanceof LeadMarketForm && !$model->getModel()->isNewRecord
				? $form->field($model, 'status')->dropDownList(LeadMarketForm::getStatusesNames())
				: ''
			?>

			<?= $form->field($model, 'details')->textarea() ?>

			<?= $model instanceof LeadMarketMultipleForm || $model->getModel()->isNewRecord
				? $form->field($model, 'withoutAddressFilter')->checkbox()
				: ''
			?>


		</div>

		<div class="col-md-6">

			<?= $this->render('_options_form', [
				'form' => $form,
				'model' => $model->getOptions(),
			])
			?>
		</div>


	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
