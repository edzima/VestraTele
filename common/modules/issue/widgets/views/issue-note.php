<?php

use common\models\issue\IssueNote;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNote */
/* @var $options array */
/* @var $editBtn bool */
/* @var $removeBtn bool */
?>

<?= Html::beginTag('div', $options) ?>
<div class="panel-heading">
	<h3 class="panel-title text-uppercase"><?= $model->title ?>
		<span class="pull-right"><?= $model->user ?></span>
	</h3>
</div>
<?php if (!empty($model->description)): ?>
	<div class="panel-body">
		<?= $model->description ?>
	</div>
<?php endif; ?>
<div class="panel-footer">
	<span class="date pull-left">
		<span class="date-publish_at">
			 <?= Yii::$app->formatter->asDateTime($model->publish_at) ?>
		</span>
		<?php if ($model->updater): ?>
			(
			<strong class="note-updater-name">
			 <?= Yii::t('issue', 'Updated by: {user}', [
				 'user' => $model->updater->getFullName(),
			 ]) ?>
			</strong>
			<span class="note-date-update_at"> - <?= Yii::$app->formatter->asDateTime($model->updated_at) ?></span>
			)
		<?php endif; ?>

	</span>

	<span class="action pull-right">
				<?= $editBtn
					? Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['note/update', 'id' => $model->id])
					: ''
				?>
				<?= $removeBtn ? Html::a('<i class="glyphicon glyphicon-trash"></i>', ['note/delete', 'id' => $model->id], [
					'data' => [
						'confirm' => 'Czy napewno chcesz usunąć?',
						'method' => 'post',
						'params' => ['id' => $model->id],
					],
				]) : '' ?>
					</span>
	<div class="clearfix"></div>
</div>
<?= Html::endTag('div') ?>
