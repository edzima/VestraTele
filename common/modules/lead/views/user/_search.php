<?php

use common\modules\lead\models\searches\LeadUsersSearch;
use common\widgets\DateWidget;
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

	<div class="row">


		<?= $form->field($model, 'dateFromAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'dateToAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>


	</div>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('lead', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
