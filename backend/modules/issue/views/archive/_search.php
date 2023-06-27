<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueArchiveSearch;
use common\widgets\DateWidget;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueArchiveSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<div id="issue-search" class="issue-search">

	<?php $form = ActiveForm::begin([
		'options' => [
			'data-pjax' => 1,
		],
		'id' => 'issue-archive-search-form',
		'method' => 'get',
		'action' => null,
	]); ?>

	<div class="row">


		<?= $form->field($model, 'type_id', ['options' => ['class' => 'col-md-6']])
			->widget(Select2::class, [
				'data' => IssueArchiveSearch::getTypesNames(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('type_id'),
				],
			])
		?>


		<?= $form->field($model, 'stage_id', ['options' => ['class' => 'col-md-6']])
			->widget(Select2::class, [
				'data' => IssueArchiveSearch::getStagesNames(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('stage_id'),
				],
			])
		?>

		<?= $form->field($model, 'issue_id', ['options' => ['class' => 'col-md-2']])
			->textInput()
		?>

		<?= $form->field($model, 'max_stage_change_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'),
			['index'], [
				'class' => 'btn btn-default',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
