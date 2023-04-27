<?php

use backend\helpers\Html;
use backend\models\search\PotentialClientSearch;
use common\widgets\ActiveForm;
use common\widgets\address\AddressSearchWidget;

/** @var yii\web\View $this */
/** @var PotentialClientSearch $model */
/** @var ActiveForm $form */

?>

<div class="potential-client-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>
	<?= AddressSearchWidget::widget([
		'form' => $form,
		'model' => $model->getAddressSearch(),
		'withPostalCode' => false,
	])
	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'),
			['index'], [
				'class' => 'btn btn-default',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
