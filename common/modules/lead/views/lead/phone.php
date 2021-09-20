<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/* @var $this \yii\web\View */
/* @var $model LeadPhoneSearch */
/* @var $dataProvider ActiveDataProvider */
$this->title = Yii::t('lead', 'Leads by Phone');

$this->params['breadcrumbs'][] = ['url' => ['index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="lead-phone-view">

	<h1><?= Html::encode($this->title) ?></h1>

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
		<div class="row">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'emptyText' => false,
				'itemView' => '_sameContact',
				'summaryOptions' => [
					'class' => 'col-md-12',
				],
				'itemOptions' => [
					'class' => 'col-md-6',
				],
			])
			?>
		</div>
	</div>
</div>
