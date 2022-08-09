<?php

use common\modules\lead\models\searches\LeadIssueSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadIssueSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-issue-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'issueDuplicated')->checkbox() ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
