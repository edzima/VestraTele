<?php

use common\models\issue\Issue;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssueSummonsWidget;
use common\modules\issue\widgets\IssueViewWidget;
use frontend\widgets\IssuePayCalculationGrid;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $calculationsDataProvider DataProviderInterface|null */

$this->title = $model->longId;
$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-view">
	<?= IssueViewWidget::widget([
		'model' => $model,
		'usersLinks' => false,
	]) ?>

	<?= $calculationsDataProvider !== null
	&& $calculationsDataProvider->getTotalCount() > 0
		? IssuePayCalculationGrid::widget([
			'dataProvider' => $calculationsDataProvider,
			'withIssue' => false,
			'withAgent' => false,
			'withIssueType' => false,
			'withCustomer' => false,
			'withDates' => false,
			'userProvisionsId' => Yii::$app->user->getId(),
		])
		: ''
	?>

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
