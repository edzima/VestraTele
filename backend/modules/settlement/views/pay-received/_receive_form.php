<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\settlement\models\ReceivePaysForm;
use common\widgets\DateWidget;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ReceivePaysForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-received-form">

	<?php $form = ActiveForm::begin(['id' => 'receive-pays-form']); ?>

	<?= $form->field($model, 'date')
		->widget(DateWidget::class)
	?>

	<?= $form->field($model, 'user_id')
		->widget(Select2::class, [
				'data' => ReceivePaysForm::getUsersNames(),
				'options' => [
					'placeholder' => $model->getAttributeLabel('user_id'),
				],
			]
		) ?>


	<?= $form->field($model, 'pays_ids')
		->widget(DepDrop::class, [
			'type' => DepDrop::TYPE_SELECT2,
			'data' => !empty($model->pays_ids) ? $model->getPaysData() : [],
			'options' => ['multiple' => true],
			'select2Options' => [
				'showToggleAll' => false,
			],
			'pluginOptions' => [
				'showToggleAll' => false,
				'depends' => [Html::getInputId($model, 'user_id')],
				'placeholder' => $model->getAttributeLabel('pays_ids'),
				'url' => Url::to(['user-not-transfer-pays']),
				'loading' => Yii::t('common', 'Loading...'),
			],
		])
	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('settlement', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
