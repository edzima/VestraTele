<?php

use common\models\provision\ProvisionUserSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'onlySelf', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->checkbox() ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
