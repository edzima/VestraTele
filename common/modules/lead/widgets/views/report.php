<?php

use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\LeadReport;
use common\modules\lead\widgets\LeadAnswersWidget;
use yii\web\View;

/* @var $this View */
/* @var $model LeadReport */
/* @var $withDeleteButton bool */
/* @var $withUpdateButton bool */
?>

<div class="panel <?= $model->isChangeStatus() ? 'panel-success' : 'panel-primary' ?> panel-note <?= $model->isDeleted() ? 'panel-deleted panel-transparent' : '' ?>">
	<div class="panel-heading">
		<div class="panel-title pull-left">
			<?= $model->isChangeStatus()
				? Yii::t('lead', 'Change status from: {oldStatus} to: {status}', [
					'status' => $model->status->name,
					'oldStatus' => $model->oldStatus->name,
				])
				: Html::encode($model->status->name)
			?>
		</div>
		<div class="panel-title pull-right">
			<?= Html::encode($model->owner) ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php if (!empty($model->details) || !empty($model->answers)) : ?>
		<div class="panel-body">
			<?php if ($model->details): ?>
				<p>
					<?= Html::encode($model->details) ?>
				</p>
			<?php endif; ?>
			<?= LeadAnswersWidget::widget([
				'answers' => $model->answers,
			]) ?>
		</div>
	<?php endif; ?>

	<div class="panel-footer">
		<span class="date pull-left">
			<?= $model->formattedDates ?>
			<?= $model->isDeleted()
				? '<strong>' . Yii::t('lead', 'Deleted At: {date}', [
					'date' => Yii::$app->formatter->asDatetime($model->deleted_at),
				]) . '</strong>'
				: ''
			?>
		</span>
		<?php if ($model->owner_id === Yii::$app->user->id || Yii::$app->user->can(User::ROLE_ADMINISTRATOR)): ?>
			<span class="action pull-right">
				<?= $withUpdateButton || Yii::$app->user->can(User::ROLE_ADMINISTRATOR)
					? Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['/lead/report/update', 'id' => $model->id])
					: ''
				?>
				<?= $withDeleteButton || Yii::$app->user->can(User::ROLE_ADMINISTRATOR)
					? Html::a('<i class="glyphicon glyphicon-trash"></i>', ['/lead/report/delete', 'id' => $model->id], [
						'data' => [
							'confirm' => Yii::t('lead', 'Are you sure you want to delete this report?'),
							'method' => 'POST',
							'params' => ['id' => $model->id],
						],
					])
					: ''
				?>
					</span>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
</div>

