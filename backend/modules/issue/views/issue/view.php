<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\issue\widgets\IssueSmsButtonDropdown;
use backend\modules\issue\widgets\StageChangeButtonDropdown;
use backend\modules\issue\widgets\SummonCreateButtonDropdown;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use backend\widgets\GridView;
use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\IssueViewWidget;
use common\modules\lead\models\LeadIssue;
use common\modules\lead\widgets\LeadIssueActionColumn;
use common\modules\lead\widgets\LeadUsersColumn;
use yii\bootstrap\ButtonDropdown;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $calculationsDataProvider DataProviderInterface */
/* @var $summonDataProvider DataProviderInterface */

$this->title = $model->longId;
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);

?>


<?=
GridView::widget([
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getLinkedLeads()
			->orderBy(['lead.date_at' => SORT_ASC])
			->joinWith('lead')
			->with('lead.leadUsers.user.userProfile')
			->with('lead.leadSource'),

	]),
	'columns' => [
		[
			'attribute' => 'lead_id',
			'value' => static function (LeadIssue $leadIssue): string {
				return Html::a(Html::encode($leadIssue->lead->getName()), ['/lead/lead/view', 'id' => $leadIssue->lead_id]);
			},
			'format' => 'html',
		],
		'lead.statusName',
		'lead.leadSource.name',
		'lead.date_at:date',
		[
			'class' => LeadUsersColumn::class,
			'attribute' => 'lead.leadUsers',
		],
		'confirmed_at:date',
		[
			'class' => LeadIssueActionColumn::class,
		],
	],
])
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


		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE)
			? Html::a(Yii::t('backend', 'Link'), ['relation/create', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= !$model->isArchived() && Yii::$app->user->can(Worker::PERMISSION_ISSUE_LINK_USER)
			? Html::a(Yii::t('backend', 'Link User'), ['user/link', 'issueId' => $model->id], [
				'class' => 'btn btn-success',
			])
			: ''
		?>

		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_DELETE)
			? Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger pull-right',
				'data' => [
					'confirm' => 'Czy napewno chcesz usunąć?',
					'method' => 'post',
				],
			])
			: ''
		?>


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

		<?= Yii::$app->user->can(Worker::PERMISSION_CALCULATION_TO_CREATE)
			? Html::a(
				Yii::t('backend', 'Create administrative settlement'),
				['/settlement/calculation/create-administrative', 'id' => $model->id],
				['class' => 'btn btn-success'])
			: '' ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_COST)
			? Html::a(
				Yii::t('backend', 'Costs'),
				['/settlement/cost/issue', 'id' => $model->id],
				['class' => 'btn btn-warning'])
			: '' ?>
	</p>


	<?= IssueNotesWidget::widget([
		'model' => $model,
		'notes' => $model->getIssueNotes()->joinWith('user.userProfile')->pinned()->all(),
		'title' => Yii::t('issue', 'Pinned Issue Notes'),
	]) ?>



	<?= $calculationsDataProvider->getTotalCount() > 0
		? IssuePayCalculationGrid::widget([
			'dataProvider' => $calculationsDataProvider,
			'caption' => Yii::t('settlement', 'Settlements'),
			'withIssue' => false,
			'summary' => '',
			'withIssueType' => false,
			'withCustomer' => false,
			'withDates' => false,
		])
		: ''
	?>

	<?= IssueViewWidget::widget([
		'model' => $model,
		'relationActionColumn' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE),
		'claimActionColumn' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CLAIM),
	]) ?>

	<?= $this->render('_summon', [
		'dataProvider' => $summonDataProvider,
	]) ?>

	<?= IssueNotesWidget::widget(['model' => $model]) ?>

</div>
