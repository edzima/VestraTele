<?php

use common\models\issue\IssuePay;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssuePay */
$this->title = 'Edytuj status wpłaty: ' . Html::encode($model->issue->getClientFullName());
$this->params['breadcrumbs'][] = ['label' => 'Wpłaty', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Status';
?>
<div class="issue-note-update">

	<h1><?= Html::encode($this->title) ?></h1>
	<div class="issue-pay-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'status')->dropDownList(IssuePay::getStatusNames()) ?>

		<div class="form-group">
			<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>


</div>
