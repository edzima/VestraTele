<?php

use common\models\issue\Issue;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssuePaysWidget;
use common\modules\issue\widgets\IssueSummonsWidget;
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
		'user' => Yii::$app->user->getIdentity(),
	]) ?>
	<?= IssueSummonsWidget::widget([
		'model' => $model,
		'addBtn' => false,
		'baseUrl' => '/summon/',
		'actionColumnTemplate' => '{view} {update}',
	]) ?>

	<?= IssueNotesWidget::widget([
		'model' => $model,
		'addBtn' => Yii::$app->user->can(User::PERMISSION_NOTE),
		'noteOptions' => [
			'removeBtn' => false,
		],
	]) ?>
</div>
