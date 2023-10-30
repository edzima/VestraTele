<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use backend\widgets\CsvForm;
use common\behaviors\IssueTypeParentIdAction;
use common\models\user\Worker;
use common\widgets\grid\SelectionForm;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Issues');

if (!$searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
} else {
	$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => IssueTypeParentIdAction::urlAll()];
	$this->params['breadcrumbs'][] = ['label' => $searchModel->getIssueMainType()->name];
}
$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/issue/index'],
];
?>
<div class="issue-index">

	<?php Pjax::begin([
		'timeout' => 2000,
	]); ?>

	<div class="clearfix form-group">

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON)
			? Html::a(Yii::t('common', 'Summons'), ['/issue/summon/index', 'parentTypeId' => $searchModel->parentTypeId], [
				'class' => 'btn btn-warning',
				'data-pjax' => 0,
			])
			: ''
		?>
		<?= Yii::$app->user->can(Worker::PERMISSION_NOTE)
			? Html::a(Yii::t('issue', 'Issue Notes'), ['note/index'], [
				'class' => 'btn btn-info',
				'data-pjax' => 0,
			])
			: ''
		?>
		<?= Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
			? Html::a(Yii::t('backend', 'Settlements'), ['/settlement/calculation/index'], [
				'class' => 'btn btn-success',
				'data-pjax' => 0,
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_STAGE_CHANGE)
			? Html::a('<i class="fa fa-calendar"></i>' . ' ' . Yii::t('issue', 'Stages Deadlines'),
				['/calendar/issue-stage-deadline/index', 'parentTypeId' => $searchModel->getIssueMainType()->id ?? null,],
				[
					'class' => 'btn btn-warning',
					'data-pjax' => 0,
				])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_TAG_MANAGER)
			? Html::a(Html::icon('tags'), ['tag/index'],
				[
					'class' => 'btn btn-success',
					'title' => Yii::t('common', 'Tags'),
					'aria-label' => Yii::t('common', 'Tags'),
					'data-pjax' => 0,
				])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)
			? Html::a('<i class="fa fa-archive"></i>', ['archive/index'],
				[
					'class' => 'btn btn-danger',
					'title' => Yii::t('issue', 'Archive'),
					'aria-label' => Yii::t('issue', 'Archive'),
					'data-pjax' => 0,
				])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_EXPORT)
			? CsvForm::widget([
				'formOptions' => ['class' => 'pull-right'],
			])
			: ''
		?>

	</div>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<div class="grid-selection-links-wrapper">


		<?php
		SelectionForm::begin([
			'formWrapperSelector' => '.selection-form-wrapper',
			'gridId' => 'issues-list',
		]);
		?>

		<p class="selection-form-wrapper hidden">
			<?= Yii::$app->user->can(Worker::PERMISSION_MULTIPLE_SMS)
			&& !empty($dataProvider->getModels())
			&& $dataProvider->pagination->pageCount > 1
				? Html::a(
					Yii::t('backend', 'Send SMS: {count}', [
						'count' => count($searchModel->getAllIds($dataProvider->query)),
					]), [
					'sms/push-multiple',
				],
					[
						'data' => [
							'pjax' => '0',
							'method' => 'POST',
							'params' => [
								'ids' => $searchModel->getAllIds($dataProvider->query),
							],
						],
						'class' => 'btn btn-success',
					]
				)
				: ''
			?>

			<?= Yii::$app->user->can(Worker::PERMISSION_MULTIPLE_SMS)
				? Html::submitButton(
					Yii::t('backend', 'Send SMS'),
					[
						'class' => 'btn btn-success',
						'name' => 'route',
						'value' => 'sms/push-multiple',
						'data-pjax' => '0',
					])
				: ''
			?>


			<?= Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
			&& !empty($dataProvider->getModels())
			&& $dataProvider->pagination->pageCount > 1
				? Html::a(
					Yii::t('issue', 'Update Type: {count}', [
						'count' => count($searchModel->getAllIds($dataProvider->query)),
					]), [
					'type/update-multiple',
				],
					[
						'data' => [
							'pjax' => '0',
							'method' => 'POST',
							'params' => [
								'ids' => $searchModel->getAllIds($dataProvider->query),
							],
						],
						'class' => 'btn btn-info',
					]
				)
				: ''
			?>

			<?= Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
				? Html::submitButton(
					Yii::t('issue', 'Update Type'),
					[
						'class' => 'btn btn-info',
						'name' => 'route',
						'value' => 'type/update-multiple',
						'data-pjax' => '0',
					])
				: ''
			?>
		</p>
	</div>


	<?php
	//@todo remove this after migrate BS4 (add data-boundary="viewport")
	//@see https://stackoverflow.com/questions/26018756/bootstrap-button-drop-down-inside-responsive-table-not-visible-because-of-scroll#answer-51992907
	$this->registerJs("$('.table-responsive').on('show.bs.dropdown', function () {
	     $('.table-responsive').css('overflow', 'inherit' );
		});

		$('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( 'overflow', 'auto' );
	})"
	);
	?>


	<?= $this->render('_grid', [
		'dataProvider' => $dataProvider,
		'searchModel' => $searchModel,
	]) ?>

	<?php
	SelectionForm::end();
	Pjax::end();
	?>


</div>
