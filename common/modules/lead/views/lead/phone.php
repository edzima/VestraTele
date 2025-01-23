<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\modules\lead\widgets\CreateLeadBtnWidget;
use common\modules\lead\widgets\SameContactsGridView;
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

	<p>
		<?= !empty($model->phone) ? CreateLeadBtnWidget::widget([
			'owner_id' => Yii::$app->user->getId(),
			'phone' => $model->phone,
		]) : '' ?>
	</p>

	<div class="lead-phone-search">

		<div class="row">
			<div class="col-xs-12 col-sm-5 col-md-4 col-lg-2">


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
		</div>


	</div>

	<div class="lead-phone-models">


		<?= SameContactsGridView::widget([
			'dataProvider' => $dataProvider,
			'withType' => true,
		]) ?>


	</div>
</div>
