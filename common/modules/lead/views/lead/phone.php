<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\modules\lead\widgets\CreateLeadBtnWidget;
use common\modules\lead\widgets\SameContactsListWidget;
use common\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model LeadPhoneSearch */
/* @var $dataProvider ActiveDataProvider */
$this->title = Yii::t('lead', 'Leads by Phone');

$this->params['breadcrumbs'][] = ['url' => ['index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="lead-phone-view">

	<h1><?= Html::encode($this->title) ?></h1>
	<p>
		<?= !empty($model->phone) ? CreateLeadBtnWidget::widget([
			'owner_id' => Yii::$app->user->getId(),
			'phone' => $model->phone,
		]) : '' ?>
	</p>

	<div class="lead-phone-search">

		<?php $form = ActiveForm::begin([
			'action' => ['phone'],
			'method' => 'get',
		]); ?>

		<?= $form->field($model, 'phone')->textInput() ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>

	<div class="lead-phone-models">
		<?= SameContactsListWidget::widget([
			'withReportBtn' => false,
			'dataProvider' => $dataProvider,
			'withType' => true,
			'withHeader' => false,
			'summaryOptions' => [
				'class' => 'col-md-12',
			],
			'itemOptions' => [
				'class' => 'col-md-6',
			],
		]) ?>
	</div>
</div>
