<?php

use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadAnswersWidget;
use common\modules\lead\widgets\LeadReportWidget;
use yii\widgets\DetailView;

/* @var $this \yii\web\View */
/* @var $model ActiveLead */

?>

<div class="same-contact-lead">

	<h3>
		<?= Yii::$app->user->can(User::ROLE_MANAGER) || $model->isForUser(Yii::$app->user->getId())
			? Html::a(Html::encode($model->getName()), ['view', 'id' => $model->getId()])
			: Html::encode($model->getName())
		?>
	</h3>
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
			'owner',
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
				'withDelete' => false,
			]) ?>


		<?php endforeach; ?>
	<?php endif; ?>


</div>
