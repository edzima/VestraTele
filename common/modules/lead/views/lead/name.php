<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\searches\LeadNameSearch;
use common\modules\lead\widgets\CreateLeadBtnWidget;
use common\widgets\ActiveForm;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model LeadNameSearch */
/* @var $dataProvider ActiveDataProvider */
$this->title = Yii::t('lead', 'Leads by Name');

$this->params['breadcrumbs'][] = ['url' => ['index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="lead-name-view">

	<h1><?= Html::encode($this->title) ?></h1>
	<p>
		<?= !empty($model->phone) ? CreateLeadBtnWidget::widget([
			'owner_id' => Yii::$app->user->getId(),
			'phone' => $model->phone,
		]) : '' ?>
	</p>

	<div class="lead-name-search">

		<?php $form = ActiveForm::begin([
			'action' => ['name'],
			'method' => 'get',
		]); ?>

		<?= $form->field($model, 'name')->textInput() ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>

	<div class="lead-name-models">
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $model,
			'columns' => [
				'name',
				[
					'attribute' => 'type_id',
					'value' => 'typeName',
					'label' => Yii::t('lead', 'Type'),
					'filter' => LeadNameSearch::getTypesNames(),
				],
				[
					'attribute' => 'status_id',
					'value' => 'statusName',
					'label' => Yii::t('lead', 'Status'),
					'filter' => LeadNameSearch::getStatusNames(),
				],
				[
					'attribute' => 'owner',
					'format' => 'html',
					'value' => function (ActiveLead $lead): ?string {
						if ($lead->owner) {
							return $lead->owner->getEmail()
								?
								Html::mailto(
									Html::encode($lead->owner->getFullName()),
									$lead->owner->getEmail()
								)
								: Html::encode($lead->owner->getEmail());
						}
						return null;
					},
				],
				'date_at:date',
			],
		])
		?>
	</div>
</div>
