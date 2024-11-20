<?php

use common\models\provision\ProvisionUserData;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserData */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchUrl string */
?>

<div class="provision-type-form">

	<?php $form = ActiveForm::begin([
			'id' => 'provision-user-search-form',
			'method' => 'GET',
			'action' => $searchUrl,
		]
	); ?>


	<div class="row">
		<?= $form->field($model, 'date', ['options' => ['class' => 'col-md-3 col-lg-2']])->widget(DateWidget::class) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
