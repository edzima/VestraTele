<?php

use common\widgets\DateWidget;
use common\widgets\LastCurrentNextMonthNav;
use frontend\models\search\IssuePaySearch;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssuePaySearch */
/* @var $form yii\widgets\ActiveForm */

$action = Yii::$app->controller->action->id;
?>

<div class="issue-pay-search">

	<?php $form = ActiveForm::begin([
		'action' => [$action, 'status' => $model->getPayStatus()],
		'method' => 'get',
	]); ?>

	<div class="fields-wrapper">

		<div class="date-range-fields-wrapper" style="margin-left:auto;display: flex;align-items: center;">

			<?= $model->isNotPayed()
				? $form->field($model, 'delay')->dropDownList(IssuePaySearch::getDelaysRangesNames())
				: ''
			?>

			<?= $form->field($model, 'deadlineAtFrom')
				->widget(DateWidget::class) ?>

			<?= $form->field($model, 'deadlineAtTo')
				->widget(DateWidget::class) ?>

			<?= LastCurrentNextMonthNav::widget([
				'model' => $model,
				'dateFromAttribute' => 'deadlineAtFrom',
				'dateToAttribute' => 'deadlineAtTo',
				'extraParams' => [
					'status' => $model->getPayStatus(),
				],
			]) ?>


		</div>


	</div>

	<div class="form-group">
	</div>
	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', [$action, 'status' => $model->getPayStatus()], ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
