<?php

use common\models\issue\IssueNote;
use common\models\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNote */
?>

<div class="panel panel-primary panel-note">
	<div class="panel-heading">
		<h3 class="panel-title"><?= $model->title ?></h3>
	</div>
	<div class="panel-body">
		<?= $model->description ?>
	</div>
	<div class="panel-footer">
		<span class="date pull-left"><?= $model->updated_at ?></span>
		<?php if ($model->user_id === Yii::$app->user->id || Yii::$app->user->can(User::ROLE_ADMINISTRATOR)): ?>
			<span class="action pull-right">

				<?= Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['note/update', 'id' => $model->id]) ?>
				<?= Html::a('<i class="glyphicon glyphicon-trash"></i>', ['note/delete', 'id' => $model->id], [
					'data' => [
						'confirm' => 'Czy napewno chcesz usunąć?',
						'method' => 'post',
					],
				]) ?>
					</span>

		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
</div>
