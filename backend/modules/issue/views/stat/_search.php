<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueStats;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;

/* @var $this yii\web\View */
/* @var $model IssueStats */
/* @var $form ActiveForm */
var_dump($model->endAt);
?>

<div id="issue-search" class="issue-search">


	<?php $form = ActiveForm::begin([
		'options' => [
			'data-pjax' => 1,
		],
		'id' => 'issue-stat-search-form',
		'method' => 'get',
		'action' => null,
	]); ?>

	<div class="row">

		<?= $form->field($model, 'startAt', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'endAt', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'),
			'', [
				'class' => 'btn btn-default',
			]) ?>
	</div>


	<?php ActiveForm::end(); ?>

</div>
