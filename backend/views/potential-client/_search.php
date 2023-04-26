<?php

use backend\helpers\Html;
use backend\models\search\PotentialClientSearch;
use common\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var PotentialClientSearch $model */
/** @var ActiveForm $form */

?>

<div class="potential-client-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'firstname') ?>

	<?= $form->field($model, 'lastname') ?>

	<?= $form->field($model, 'details') ?>

	<?= $form->field($model, 'city_id') ?>

	<?php // echo $form->field($model, 'birthday') ?>

	<?php // echo $form->field($model, 'status') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'owner_id') ?>

	<?php // echo $form->field($model, 'phone') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
