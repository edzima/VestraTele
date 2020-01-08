<?php

use common\models\provision\ProvisionUserSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'onlySelf')->checkbox() ?>
	<?= $form->field($model, 'onlyNotDefault')->checkbox() ?>


	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
