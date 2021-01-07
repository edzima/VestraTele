<?php

use common\models\settlement\PayReceivedForm;
use common\widgets\DateWidget;
use common\widgets\settlement\SettlementDetailView;
use frontend\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PayReceivedForm */

$this->title = Yii::t('settlement', 'Received pay: {value}', [
	'value' => Yii::$app->formatter->asCurrency($model->getPay()->getValue()),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['settlement/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getPay()->calculation->getTypeName(), 'url' => ['settlement/view', 'id' => $model->getPay()->calculation_id]];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-pay-received-received">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= SettlementDetailView::widget(['model' => $model->getPay()->calculation]) ?>

	<div class="issue-pay-form">

		<?php $form = ActiveForm::begin(); ?>

		<div class="row">

			<?= $form->field($model, 'date', ['options' => ['class' => 'col-md-2']])
				->widget(DateWidget::class)
			?>
		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>


</div>
