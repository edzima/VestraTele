<?php

use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadAnswersWidget;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model ActiveLead */
?>

<div class="lead-report-detail">
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
		<?= LeadAnswersWidget::widget([
			'answers' => $model->answers,
		]) ?>
</div>
