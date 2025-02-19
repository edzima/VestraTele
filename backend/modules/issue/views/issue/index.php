<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use common\behaviors\IssueTypeParentIdAction;
use common\models\user\Worker;
use common\widgets\grid\SelectionForm;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $queryParams array */

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

$count = $dataProvider->getTotalCount();

?>
<div class="issue-index">

	<?php Pjax::begin([
		'timeout' => 2000,
	]); ?>


	<?= $this->render('_top-buttons', [
		'parentTypeId' => $searchModel->parentTypeId,
	]) ?>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= $this->render('_chart', [
		'model' => $searchModel,
		'dataProvider' => $dataProvider,
	]) ?>

	<div class="grid-selection-links-wrapper">


		<?php

		SelectionForm::begin([
			'formWrapperSelector' => '.selection-form-wrapper',
			'gridId' => 'issues-list',
		]);
		?>

		<p class="selection-form-wrapper hidden">

			<?php if (Yii::$app->user->can(Worker::PERMISSION_MULTIPLE_SMS)): ?>
				<span class="btn-group">
				<?= Html::submitButton(
					Html::faicon('envelope')
					,
					[
						'class' => 'btn btn-success',
						'name' => 'route',
						'value' => 'sms/push-multiple',
						'data-pjax' => '0',
						'title' => Yii::t('backend', 'Send SMS'),
						'aria-label' => Yii::t('backend', 'Send SMS'),
					])
				?>

				<?=
				!empty($dataProvider->getModels())
				&& $dataProvider->pagination->pageCount > 1
					? Html::a(
					$count, [
					'sms/push-multiple',
				],
					[
						'data' => [
							'pjax' => '0',
							'method' => 'POST',
							'params' => $queryParams,
						],
						'class' => 'btn btn-success',
					]
				)
					: ''
				?>
			</span>

			<?php endif; ?>


			<?php if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_TYPE_MANAGER)): ?>

				<span class="btn-group">
				<?= Html::submitButton(
					'<i class="fa fa-sitemap"></i>',
					[
						'class' => 'btn btn-info',
						'name' => 'route',
						'value' => 'type/update-multiple',
						'data-pjax' => '0',
						'title' => Yii::t('issue', 'Update Type'),
						'aria-label' => Yii::t('issue', 'Update Type'),
					])
				?>
				<?= !empty($dataProvider->getModels())
				&& $dataProvider->pagination->pageCount > 1
					? Html::a($count, [
						'type/update-multiple',
					],
						[
							'data' => [
								'pjax' => '0',
								'method' => 'POST',
								'params' => $queryParams,
							],
							'class' => 'btn btn-info',
							'title' => Yii::t('issue', 'Update Type: {count}', [
								'count' => $count,
							]),
							'aria-label' => Yii::t('issue', 'Update Type: {count}', [
								'count' => $count,
							]),
						]
					)
					: ''
				?>
			</span>

			<?php endif; ?>

			<?php if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_TAG_MANAGER)): ?>

				<span class="btn-group">
				<?= Html::submitButton(
					Html::icon('tag'),
					[
						'class' => 'btn btn-success',
						'name' => 'route',
						'value' => 'tag/link-multiple',
						'data-pjax' => '0',
						'title' => Yii::t('issue', 'Tags'),
						'aria-label' => Yii::t('issue', 'Tags'),
					])
				?>
				<?= !empty($dataProvider->getModels())
				&& $dataProvider->pagination->pageCount > 1
					? Html::a($count, [
						'tag/link-multiple',
					],
						[
							'data' => [
								'pjax' => '0',
								'method' => 'POST',
								'params' => $queryParams,
							],
							'class' => 'btn btn-success',
							'title' => Yii::t('issue', 'Link Tags: {count}', [
								'count' => $count,
							]),
							'aria-label' => Yii::t('issue', 'Link Tags: {count}', [
								'count' => $count,
							]),
						]
					)
					: ''
				?>
			</span>

			<?php endif; ?>

			<?php if (Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)): ?>

				<span class="btn-group">
				<?= Html::submitButton(
					Html::faicon('bolt'),
					[
						'class' => 'btn btn-warning',
						'name' => 'route',
						'value' => 'summon/create-multiple',
						'data-pjax' => '0',
						'title' => Yii::t('issue', 'Summons'),
						'aria-label' => Yii::t('issue', 'Summons'),
					])
				?>
				<?= !empty($dataProvider->getModels())
				&& $dataProvider->pagination->pageCount > 1
					? Html::a($count, [
						'summon/create-multiple',
					],
						[
							'data' => [
								'pjax' => '0',
								'method' => 'POST',
								'params' => $queryParams,
							],
							'class' => 'btn btn-warning',
							'title' => Yii::t('backend', 'Create Summons: {count}', [
								'count' => $count,
							]),
							'aria-label' => Yii::t('backend', 'Create Summons: {count}', [
								'count' => $count,
							]),
						]
					)
					: ''
				?>
			</span>

			<?php endif; ?>


		</p>
	</div>


	<?= $this->render('_grid', [
		'dataProvider' => $dataProvider,
		'searchModel' => $searchModel,
		'gridId' => 'issues-list',
	]) ?>

	<?php
	SelectionForm::end();
	Pjax::end();
	?>


</div>
