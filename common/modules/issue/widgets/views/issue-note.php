<?php

use common\models\issue\IssueNote;
use common\models\user\Worker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNote */
/* @var $options array */
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
	<span class="date pull-left"><?= Yii::$app->formatter->asDateTime($model->publish_at) ?></span>
	<?php if ($model->user_id === Yii::$app->user->id || Yii::$app->user->can(Worker::ROLE_MANAGER)): ?>
		<span class="action pull-right">
				<?= !$model->isSms()
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
	<?php endif; ?>
	<div class="clearfix"></div>
</div>
<?= Html::endTag('div') ?>
