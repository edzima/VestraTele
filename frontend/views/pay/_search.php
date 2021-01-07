<?php

use common\widgets\DateWidget;
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

			<?= Nav::widget([
				'items' => [
					[
						'label' => 'Poprzedni (' . date('Y-m', strtotime('last month')) . ')',
						'url' => [
							$action,
							'status' => $model->getPayStatus(),
							Html::getInputName($model, 'deadlineAtFrom') => date('Y-m-d', strtotime('first day of last month')),
							Html::getInputName($model, 'deadlineAtTo') => date('Y-m-d', strtotime('last day of last month')),

						],
						'active' => $model->deadlineAtFrom === date('Y-m-d', strtotime('first day of last month'))
							&& $model->deadlineAtTo === date('Y-m-d', strtotime('last day of last month')),
					],
					[
						'label' => 'Obecny (' . date('Y-m') . ')',
						'url' => [
							$action,
							'status' => $model->getPayStatus(),
							Html::getInputName($model, 'deadlineAtFrom') => date('Y-m-d', strtotime('first day of this month')),
							Html::getInputName($model, 'deadlineAtTo') => date('Y-m-d', strtotime('last day of this month')),

						],
						'active' => $model->deadlineAtFrom === date('Y-m-d', strtotime('first day of this month'))
							&& $model->deadlineAtTo === date('Y-m-d', strtotime('last day of this month')),
					],
					[
						'label' => 'Następny (' . date('Y-m', strtotime('next month')) . ')',
						'url' => [
							$action,
							'status' => $model->getPayStatus(),
							Html::getInputName($model, 'deadlineAtFrom') => date('Y-m-d', strtotime('first day of next month')),
							Html::getInputName($model, 'deadlineAtTo') => date('Y-m-d', strtotime('last day of next month')),
						],
						'active' => $model->deadlineAtFrom === date('Y-m-d', strtotime('first day of next month'))
							&& $model->deadlineAtTo === date('Y-m-d', strtotime('last day of next month')),
					],
				],
				'options' => ['class' => 'nav-pills'],

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
