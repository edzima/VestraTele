<?php

use backend\modules\issue\models\search\IssueNoteSearch;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueNoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-note-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>
	</div>

	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'),
			['index'], [
				'class' => 'btn btn-default',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
