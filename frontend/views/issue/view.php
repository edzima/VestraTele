<?php

use common\models\issue\Issue;
use common\models\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssuePaysWidget;
use common\modules\issue\widgets\IssueViewWidget;

/* @var $this yii\web\View */
/* @var $model Issue */

$this->title = $model->longId;
$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-view">
	<?= IssueViewWidget::widget(['model' => $model]) ?>
	<?= IssuePaysWidget::widget([
		'models' => $model->pays,
		'editPayBtn' => false,
		'user' => !Yii::$app->user->can(User::ROLE_BOOKKEEPER) ? Yii::$app->user->getIdentity() : null,
	]) ?>
	<?= IssueNotesWidget::widget([
		'model' => $model,
		'addBtn' => Yii::$app->user->can(User::ROLE_NOTE),
		'noteOptions' => [
			'removeBtn' => false,
		],
	]) ?>
</div>
