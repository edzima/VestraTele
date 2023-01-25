<?php

use common\models\issue\Issue;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssueViewWidget;
use frontend\helpers\Breadcrumbs;
use frontend\helpers\Html;
use frontend\widgets\issue\StageChangeButtonDropdown;
use frontend\widgets\issue\SummonCreateButtonDropdown;
use frontend\widgets\IssuePayCalculationGrid;
use frontend\widgets\SummonGrid;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $calculationsDataProvider DataProviderInterface|null */
/* @var $summonDataProvider DataProviderInterface|null */

$this->title = $model->longId;
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);

?>
<div class="issue-view">


	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_STAGE_CHANGE)
			? StageChangeButtonDropdown::widget([
				'model' => $model,
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON_CREATE)
			? SummonCreateButtonDropdown::widget([
				'issueId' => $model->getIssueId(),
			])
			: ''
		?>


		<?= Yii::$app->user->can(Worker::PERMISSION_NOTE) || Yii::$app->user->can(Worker::PERMISSION_NOTE_SELF)
			? Html::a(Yii::t('common', 'Create note'), ['/note/issue', 'id' => $model->id], [
				'class' => 'btn btn-info',
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SMS)
			? Html::a(Yii::t('common', 'Send SMS'), ['/issue-sms/push', 'id' => $model->id], [
				'class' => 'btn btn-warning',
			])
			: ''
		?>
	</p>

	<?= IssueNotesWidget::widget([
		'model' => $model,
		'notes' => $model->getIssueNotes()->joinWith('user.userProfile')->pinned()->all(),
		'title' => Yii::t('issue', 'Pinned Issue Notes'),
	]) ?>

	<?= $calculationsDataProvider !== null
	&& $calculationsDataProvider->getTotalCount() > 0
		? IssuePayCalculationGrid::widget([
			'dataProvider' => $calculationsDataProvider,
			'withIssue' => false,
			'withAgent' => false,
			'withCaption' => true,
			'withIssueType' => false,
			'withCustomer' => false,
			'withDates' => false,
			'userProvisionsId' => Yii::$app->user->getId(),
		])
		: ''
	?>

	<?= IssueViewWidget::widget([
		'model' => $model,
		'usersLinks' => false,
		'relationActionColumn' => false,
		'claimActionColumn' => false,
		'userMailVisibilityCheck' => true,
	]) ?>

	<?= $summonDataProvider->getTotalCount() > 0
		? SummonGrid::widget([
			'dataProvider' => $summonDataProvider,
			'summary' => '',
			'withTitle' => false,
			'withDocs' => false,
			'withTitleWithDocs' => true,
			'withCaption' => true,
			'withIssue' => false,
			'withCustomer' => false,
			'withOwner' => false,
			'withContractor' => true,
			'withUpdatedAt' => false,
		])
		: ''
	?>

	<?= IssueNotesWidget::widget([
		'model' => $model,
	]) ?>
</div>
