<?php

use common\models\issue\IssueRelation;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueRelation */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="issue-relation-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">


		<?= $form->field($model, 'issue_id_2', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput() ?>
	</div>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
