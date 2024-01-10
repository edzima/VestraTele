<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\issue\widgets\IssueSmsButtonDropdown;
use backend\modules\issue\widgets\IssueViewSummonsWidgets;
use backend\modules\issue\widgets\IssueViewTopSummonsWidgets;
use backend\modules\issue\widgets\StageChangeButtonDropdown;
use backend\modules\issue\widgets\SummonCreateButtonDropdown;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssueViewWidget;
use common\modules\issue\widgets\SummonDocsWidget;
use yii\bootstrap\ButtonDropdown;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $calculationsDataProvider DataProviderInterface */
/* @var $summonDataProvider DataProviderInterface */

$this->title = $model->getIssueModel()->customer->getFullName() . ' - ' . $model->getIssueName();
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);

?>
<div class="issue-view">
	<p>


		<?= StageChangeButtonDropdown::widget([
			'model' => $model,
		])
		?>

		<?= (Yii::$app->user->can(Worker::PERMISSION_SUMMON_CREATE)
			|| Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER))
			? SummonCreateButtonDropdown::widget([
				'issueId' => $model->getIssueId(),
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SMS)
			? IssueSmsButtonDropdown::widget([
				'model' => $model,
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_NOTE)
			? Html::a(Yii::t('backend', 'Create note'), ['note/create', 'issueId' => $model->id], [
				'class' => 'btn btn-info',
			])
			: ''
		?>


		<span class="pull-right">

					<?= Html::a(
						Html::faicon('pencil'),
						['update', 'id' => $model->id],
						[
							'class' => 'btn btn-primary',
							'title' => Yii::t('backend', 'Update'),
							'aria-label' => Yii::t('backend', 'Update'),
						]) ?>


					<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE)
						? Html::a(Html::faicon('copy'),
							['issue/create-and-link', 'id' => $model->id],
							[
								'class' => 'btn btn-primary',
								'title' => Yii::t('issue', 'Create & Link'),
								'aria-label' => Yii::t('issue', 'Create & Link'),
							])
						: ''
					?>

					<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE)
						? Html::a(Html::faicon('link'),
							['relation/create', 'id' => $model->id],
							[
								'class' => 'btn btn-primary',
								'title' => Yii::t('backend', 'Link'),
								'aria-label' => Yii::t('backend', 'Link'),
							])
						: ''
					?>

					<?= !$model->isArchived() && Yii::$app->user->can(Worker::PERMISSION_ISSUE_LINK_USER)
						? Html::a(Html::faicon('user-plus'),
							['user/link', 'issueId' => $model->id], [
								'class' => 'btn btn-success',
								'aria-label' => Yii::t('backend', 'Link User'),
								'title' => Yii::t('backend', 'Link User'),
							])
						: ''
					?>

					<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE)
						? Html::a(
							Html::icon('tag'),
							['tag/issue', 'issueId' => $model->id],
							[
								'title' => Yii::t('common', 'Tags'),
								'aria-label' => Yii::t('common', 'Tags'),
								'class' => 'btn btn-success',
							]
						)
						: ''
					?>

					<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_SHIPMENT)
						? Html::a(
							Html::faicon('envelope-open-o'),
							['shipment-poczta-polska/create', 'issueId' => $model->id], [
								'title' => Yii::t('backend', 'Add a Shipment'),
								'aria-label' => Yii::t('backend', 'Add a Shipment'),
								'class' => 'btn btn-info',
							]
						)
						: ''
					?>

					<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_DELETE)
						? Html::a(Html::icon('trash'),
							['delete', 'id' => $model->id],
							[
								'title' => Yii::t('backend', 'Delete'),
								'aria-label' => Yii::t('backend', 'Delete'),
								'class' => 'btn btn-danger',
								'data' => [
									'confirm' => 'Czy napewno chcesz usunąć?',
									'method' => 'post',
								],
							])
						: ''
					?>
		</span>


	</p>
	<p>
		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_CLAIM)
			? ButtonDropdown::widget([
				'label' => Yii::t('issue', 'Create Claims'),
				'tagName' => 'a',
				'options' => [
					'href' => ['claim/create-multiple', 'issueId' => $model->id],
					'class' => 'btn btn-danger',
				],
				'split' => true,
				'dropdown' => [
					'items' => [
						[
							'label' => Yii::t('issue', 'Customer Claim'),
							'url' => [
								'claim/create',
								'issueId' => $model->id, 'type' => IssueClaim::TYPE_CUSTOMER,
							],
						],
						[
							'label' => Yii::t('issue', 'Company Claim'),
							'url' => [
								'claim/create',
								'issueId' => $model->id, 'type' => IssueClaim::TYPE_COMPANY,
							],
						],
					],
				],
			])
			: ''
		?>


		<?= Yii::$app->user->can(Worker::PERMISSION_CALCULATION_TO_CREATE)
			? Html::a(
				Yii::t('backend', 'Create settlement'),
				['/settlement/calculation/create', 'id' => $model->id],
				['class' => 'btn btn-success'])
			: '' ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SETTLEMENT_ADMINISTRATIVE_CREATE)
			? Html::a(
				Yii::t('backend', 'Create administrative settlement'),
				['/settlement/calculation/create-administrative', 'id' => $model->id],
				['class' => 'btn btn-success'])
			: '' ?>

	</p>


	<?= IssueNotesWidget::widget([
		'model' => $model,
		'notes' => $model->getIssueNotes()
			->joinWith('user.userProfile')
			->joinWith('updater.userProfile')
			->pinned()
			->all(),
		'title' => Yii::t('issue', 'Pinned Issue Notes'),
	]) ?>


	<?= SummonDocsWidget::widget([
		'models' => SummonDocsWidget::modelsFromSummons($summonDataProvider->getModels(), Yii::$app->user->getId()),
		'controller' => '/issue/summon-doc-link',
		'hideOnAllAreConfirmed' => true,
	]) ?>

	<?= $calculationsDataProvider->getTotalCount() > 0
		? IssuePayCalculationGrid::widget([
			'dataProvider' => $calculationsDataProvider,
			'caption' => Yii::t('settlement', 'Settlements'),
			'withIssue' => false,
			'summary' => '',
			'withAgent' => false,
			'withIssueType' => false,
			'withCustomer' => false,
			'withDates' => false,
		])
		: ''
	?>

	<?= IssueViewTopSummonsWidgets::widget([
		'dataProvider' => $summonDataProvider,
	]) ?>


	<?= IssueViewWidget::widget([
		'model' => $model,
		'claimActionColumn' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CLAIM),
		'relationActionColumn' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE),
		'shipmentsActionColumn' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_SHIPMENT),
		'costRoute' => '/settlement/cost',
		'entityResponsibleRoute' => '/entity-responsible/default/view',
		'stageRoute' => 'stage/view',
		'typeRoute' => 'type/view',

	]) ?>



	<?= IssueViewSummonsWidgets::widget([
		'dataProvider' => $summonDataProvider,
	]) ?>


	<?= IssueNotesWidget::widget([
		'model' => $model,
		'collapseTypes' => [IssueNotesWidget::TYPE_SMS],
		'withProvisionControl' => Yii::$app->user->can(User::PERMISSION_PROVISION),
	]) ?>

</div>
