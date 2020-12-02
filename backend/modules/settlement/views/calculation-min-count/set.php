<?php

use backend\modules\settlement\models\CalculationMinCountForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $model CalculationMinCountForm */

$this->title = Yii::t('backend', 'Set min calculation count');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Calculations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Calculations min counts'), 'url' => ['min-count-list']];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-calculation-min-count-set">


	<h1><?= Html::encode($this->title) ?></h1>


	<div class="calculation-min-count-form">

		<?php $form = ActiveForm::begin([
				'id' => 'calculation-min-count-form',
			]
		); ?>

		<div class="row">

			<?= $form->field($model, 'typeId', [
				'options' => [
					'class' => 'col-md-3',
				],
			])
				->widget(Select2::class, [
						'data' => CalculationMinCountForm::getTypesNames(),
						'options' => [
							'placeholder' => $model->getAttributeLabel('typeId'),
						],
					]
				) ?>



			<?= $form->field($model, 'stageId', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->getStagesNames(),
					'pluginOptions' => [
						'depends' => [Html::getInputId($model, 'typeId')],
						'placeholder' => $model->getAttributeLabel('stageId'),
						'url' => Url::to(['/issue/type/stages-list']),
						'loading' => Yii::t('common', 'Loading...'),
					],
				])
			?>

			<?= $form->field($model, 'minCount', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])->textInput() ?>
		</div>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>


		<?php ActiveForm::end(); ?>

	</div>

</div>

