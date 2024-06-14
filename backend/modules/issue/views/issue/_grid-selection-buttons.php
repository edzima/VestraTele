<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use common\models\user\Worker;
use common\widgets\grid\SelectionForm;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $gridId string */
?>


<div class="grid-selection-links-wrapper">


	<?php

	SelectionForm::begin([
		'formWrapperSelector' => '.selection-form-wrapper',
		'gridId' => $gridId,
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
					count($searchModel->getAllIds($dataProvider->query)), [
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
						'title' => Yii::t('backend', 'Send SMS: {count}', [
							'count' => count($searchModel->getAllIds($dataProvider->query)),
						]),
						'aria-label' => Yii::t('backend', 'Send SMS: {count}', [
							'count' => count($searchModel->getAllIds($dataProvider->query)),
						]),
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
					? Html::a(count($searchModel->getAllIds($dataProvider->query)), [
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
							'title' => Yii::t('issue', 'Update Type: {count}', [
								'count' => count($searchModel->getAllIds($dataProvider->query)),
							]),
							'aria-label' => Yii::t('issue', 'Update Type: {count}', [
								'count' => count($searchModel->getAllIds($dataProvider->query)),
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
					? Html::a(count($searchModel->getAllIds($dataProvider->query)), [
						'tag/link-multiple',
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
							'title' => Yii::t('issue', 'Link Tags: {count}', [
								'count' => count($searchModel->getAllIds($dataProvider->query)),
							]),
							'aria-label' => Yii::t('issue', 'Link Tags: {count}', [
								'count' => count($searchModel->getAllIds($dataProvider->query)),
							]),
						]
					)
					: ''
				?>
			</span>

		<?php endif; ?>


	</p>
</div>
