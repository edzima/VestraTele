<?php

use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use frontend\helpers\Html;
use frontend\models\search\IssueSearch;

/* @var $this yii\web\View */
/* @var $model IssueSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="issue-search" class="issue-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'createdAtFrom', ['options' => ['class' => 'col-md-3 col-lg-2']])
			->widget(DateTimeWidget::class) ?>

		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-3 col-lg-2']])
			->widget(DateTimeWidget::class) ?>

	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
