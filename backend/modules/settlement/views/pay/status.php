<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\IssuePay;
use common\models\user\User;
use common\widgets\settlement\SettlementDetailView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssuePay */

$this->title = Yii::t('backend', 'Pay status: {value}', ['value' => Yii::$app->formatter->asCurrency($model->getValue())]);
$issue = $model->calculation->issue;
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
	$this->params['breadcrumbs'][] = ['label' => $issue->longId, 'url' => ['/settlement/calculation/issue', 'id' => $issue->id]];
}
$this->params['breadcrumbs'][] = ['label' => $model->calculation->getTypeName(), 'url' => ['/settlement/calculation/view', 'id' => $model->calculation_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Status');
?>
<div class="issue-pay-status">

	<div class="row">
		<div class="col-md-5">
			<?= SettlementDetailView::widget([
				'model' => $model->calculation,
			]) ?>
		</div>
	</div>

	<div class="issue-pay-form">

		<?php $form = ActiveForm::begin(); ?>

		<div class="row">
			<?= $form->field($model, 'status', ['options' => ['class' => 'col-md-5']])->dropDownList(IssuePay::getStatusNames(), ['prompt' => 'Status...']) ?>
		</div>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>


</div>
