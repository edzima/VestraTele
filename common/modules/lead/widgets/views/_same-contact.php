<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\CopyLeadBtnWidget;
use common\modules\lead\widgets\LeadAnswersWidget;
use common\modules\lead\widgets\LeadDialersGridView;
use common\modules\lead\widgets\LeadReportWidget;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model ActiveLead */
/* @var $viewLink bool */
/* @var $withDialers bool */
/* @var $updateLink bool */
/* @var $visibleCustomerLink bool */
?>

<div class="same-contact-lead">

	<h3>
		<?= $viewLink || $model->isForUser(Yii::$app->user->getId())
			? Html::a(Html::encode($model->getName()), ['view', 'id' => $model->getId()])
			: Html::encode($model->getName())
		?>
	</h3>

	<p>

		<?= Html::a(Yii::t('lead', 'Create Lead Report'),
			['/lead/report/report', 'id' => $model->getId(), 'hash' => $model->getHash()],
			[
				'class' => 'btn btn-success',
			])
		?>

		<?= !$model->isForUser(Yii::$app->user->getId())
			? CopyLeadBtnWidget::widget([
				'leadId' => $model->getId(),
			])
			: ''
		?>

		<?= $updateLink
			? Html::a(Yii::t('lead', 'Update'),
				['lead/update', 'id' => $model->getId()],
				[
					'class' => 'btn btn-primary',
				])
			: ''
		?>


	</p>
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'status',
			[
				'attribute' => 'source.type',
				'label' => Yii::t('lead', 'Type'),
			],
			'source',
			'date_at:datetime',
			[
				'attribute' => 'customerUrl',
				'format' => 'html',
				'label' => Yii::t('lead', 'Customer View'),
				'visible' => $visibleCustomerLink && isset($model->getData()['customerUrl']),
				'value' => function (ActiveLead $model): ?string {
					$url = $model->getData()['customerUrl'] ?? null;
					if ($url) {
						return Html::a(Html::encode($model->getName()), $url);
					}
					return null;
				},
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
				'attribute' => 'owner',
				'format' => 'html',
				'value' => $model->owner
					? $model->owner->getEmail()
						? Html::mailto(
							Html::encode($model->owner->getFullName()),
							$model->owner->getEmail())
						: Html::encode($model->owner->getFullName())
					: null,
				'visible' => $model->owner !== null,
			],

		],
	]) ?>




	<?= LeadAnswersWidget::widget([
		'answers' => $model->answers,
	]) ?>

	<?php if (!empty($model->reports)): ?>
		<h4><?= Yii::t('lead', 'Reports') ?></h4>
		<?php foreach ($model->reports as $report): ?>

			<?= LeadReportWidget::widget([
				'model' => $report,
				'withDeleteButton' => false,
			]) ?>


		<?php endforeach; ?>
	<?php endif; ?>

	<?= $withDialers
		? LeadDialersGridView::widget([
			'lead' => $model,
		])
		: ''
	?>

</div>
