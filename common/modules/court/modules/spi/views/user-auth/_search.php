<?php

use common\modules\court\modules\spi\models\UserAuthSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var UserAuthSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="spi-user-auth-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'user_id') ?>

	<?= $form->field($model, 'created_at') ?>

	<?= $form->field($model, 'updated_at') ?>

	<?= $form->field($model, 'last_action_at') ?>

	<?php // echo $form->field($model, 'username') ?>

	<?php // echo $form->field($model, 'password') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('spi', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('spi', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
