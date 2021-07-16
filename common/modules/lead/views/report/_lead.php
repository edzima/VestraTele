<?php

use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadAnswersWidget;
use yii\widgets\DetailView;

/* @var $this \yii\web\View */
/* @var $model ActiveLead */
?>

<div class="row">
	<div class="col-md-6">
		<?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'status',
				[
					'attribute' => 'source.type',
					'label' => Yii::t('lead', 'Type'),
				],
				[
					'attribute' => 'phone',
					'format' => 'tel',
					'visible' => !empty($model->getPhone()),
				],
				[
					'attribute' => 'email',
					'format' => 'email',
					'visible' => !empty($model->getEmail()),
				],
				[
					'attribute' => 'postal_code',
					'visible' => !empty($model->getPostalCode()),
				],
				[
					'attribute' => 'providerName',
					'visible' => !empty($model->getProvider()),
				],
			],
		]) ?>
	</div>
	<div class="col-md-6">
		<?= LeadAnswersWidget::widget([
			'answers' => $model->answers,
		]) ?>
	</div>
</div>
