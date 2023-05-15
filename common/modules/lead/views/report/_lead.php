<?php

use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadAnswersWidget;
use yii\web\View;
use common\helpers\Html;
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
			[
				'attribute' => 'source',
				'format' => 'raw',
				'value' => $model->getSource()->getURL()
					? Html::a(
						Html::encode($model->getSource()->getURL()),
						$model->getSource()->getURL(), [
						'target' => '_blank',
					]) : Html::encode($model->getSource()->getName()),
				'visible' => !empty($model->getProvider()),
			],
			[
				'attribute' => 'owner',
				'format' => 'userEmail',
			],
			[
				'attribute' => 'date_at',
				'format' => 'date',
			],
		],
	]) ?>
	<?= LeadAnswersWidget::widget([
		'answers' => $model->answers,
	]) ?>
</div>
