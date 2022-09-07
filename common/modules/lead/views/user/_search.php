<?php

use common\modules\lead\models\searches\LeadUsersSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadUsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'lead_id') ?>

	<?= $form->field($model, 'user_id') ?>

	<?= $form->field($model, 'type') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('lead', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
