<?php

use common\models\issue\IssuePayCalculation;
use common\models\settlement\PaysForm;
use common\widgets\DateWidget;
use common\widgets\settlement\SettlementDetailView;
use frontend\helpers\Html;
use kartik\number\NumberControl;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $calculation IssuePayCalculation */
/* @var $model PaysForm */

$this->title = Yii::t('backend', 'Generate pays for: {id}', ['id' => $calculation->getTypeName()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $calculation->getIssueName(), 'url' => ['/issue/view', 'id' => $calculation->getIssueId()]];

$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $calculation->getTypeName(), 'url' => ['view', 'id' => $calculation->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-calculation-pays">


	<h1> <?= Html::encode($this->title) ?> </h1>
	<div class="row">
		<div class="col-md-5">
			<?= SettlementDetailView::widget([
				'model' => $calculation,
			]) ?>
		</div>
	</div>

	<div class="settlement-pays-form">

		<?php $form = ActiveForm::begin(); ?>

		<div class="row">

			<?= $form->field($model, 'count', ['options' => ['class' => 'col-md-1 col-lg-1']])
				->textInput() ?>

			<?= $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(DateWidget::class)
			?>

			<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-3 col-lg-2']])->widget(NumberControl::class, [
				'disabled' => true,
			]) ?>

		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Generate'), [
				'id' => 'generate-btn',
				'class' => 'btn btn-primary',
				'name' => 'action',
				'value' => 'generate',
			]) ?>
		</div>


		<h3><?= Yii::t('backend', 'Pays') ?></h3>


		<?php
		$i = 0;
		foreach ($model->getPays() as $index => $pay) {
			echo $this->render('_form_pay', [
				'form' => $form,
				'model' => $pay,
				'id' => $index,
				'index' => $i++,
			]);
		}
		?>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Save'), [
				'id' => 'save-btn', 'class' => 'btn btn-success',
				'name' => 'action',
				'value' => 'save',
			]) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>


</div>
