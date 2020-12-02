<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Url;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\PaysForm;
use common\widgets\DateWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $calculation IssuePayCalculation */
/* @var $model PaysForm */

$this->title = Yii::t('backend', 'Generate pays for: {id}', ['id' => $calculation->id]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($calculation->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $calculation->issue->longId, 'url' => ['issue', 'id' => $calculation->issue_id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-calculation-create">


	<h2> <?= Html::a(
			$calculation->issue->longId,
			Url::issueView($calculation->issue_id),
			['target' => '_blank']) ?>
	</h2>
	<div class="row">
		<div class="col-md-5">
			<?= DetailView::widget([
				'model' => $calculation,
				'attributes' => [
					'typeName',
					'providerName',
					'value:currency',
					'valueToPay:currency',
				],
			]) ?>
		</div>
	</div>

	<div class="settlement-pays-form">

		<?php $form = ActiveForm::begin(); ?>

		<div class="row">

			<?= $form->field($model, 'count', ['options' => ['class' => 'col-md-1 col-lg-1']])
				->textInput() ?>

			<?= $form->field($model, 'transferType', ['options' => ['class' => 'col-md-3 col-lg-2']])->dropDownList(PaysForm::getTransferTypesNames()) ?>

			<?= $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(DateWidget::class)
			?>

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

<script>
	document.addEventListener('DOMContentLoaded', function () {
		let paysCountInput = document.getElementById('paysform-count');
		let generatBtn = document.getElementById('generate-btn');

		function parsePaysCountInput() {
			const count = parseInt(paysCountInput.value);
			if (count > 1) {
				generatBtn.classList.remove('hide');
			} else {
				generatBtn.classList.add('hide');
			}
		}


	//	parsePaysCountInput();

	//	paysCountInput.addEventListener('change', parsePaysCountInput);


	})
</script>
