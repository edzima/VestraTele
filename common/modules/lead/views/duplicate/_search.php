<?php

use common\models\user\User;
use common\modules\lead\models\searches\DuplicateLeadSearch;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model DuplicateLeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-report-schema-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'status', [
			'options' => [
				'class' => [
					'col-md-3',
				],
			],
		])->dropDownList(
			DuplicateLeadSearch::getStatusFilterNames(), [
			'prompt' => Yii::t('lead', 'Select...'),
		])
		?>


		<?= $form->field($model, 'date_at', [
			'options' => [
				'class' => [
					'col-md-2',
				],
			],
		])->widget(DateWidget::class)
		?>

		<?= Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER)
			? $form->field($model, 'onlyDialers', [
				'options' => [
					'class' => [
						'col-md-2',
					],
				],
			])->checkbox()
			: ''
		?>


	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
