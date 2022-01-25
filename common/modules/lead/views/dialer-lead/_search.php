<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadDialerSearch;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use yii\web\View;

/* @var $this View */
/* @var $model LeadDialerSearch */

?>

<div class="dialer-lead-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index', 'dialerId' => $model->getDialerId()],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'from_at', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'to_at', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->widget(DateWidget::class)
		?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Reset'), ['index', 'dialerId' => $model->getDialerId()], ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>




