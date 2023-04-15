<?php

use backend\widgets\CsvForm;
use common\helpers\Html;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\lead\models\searches\LeadSearch;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $assignUsers bool */
$dataProvider->getModels();

?>


<?= Html::beginForm() ?>

<?php if ($assignUsers): ?>
	<div class="btn-group">

		<?= Html::submitButton(
			'<i class="fa fa-user-plus" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-info',
				'name' => 'route',
				'value' => 'user/assign',
				'title' => Yii::t('lead', 'Link Users'),
				'aria-label' => Yii::t('lead', 'Link Users'),
			])
		?>
		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)),
				['user/assign'],
				[
					'class' => 'btn btn-info',
					'title' => Yii::t('lead', 'Link Users ({count})', ['count' => count($searchModel->getAllIds($dataProvider->query))]),
					'aria-label' => Yii::t('lead', 'Link Users ({count})', ['count' => count($searchModel->getAllIds($dataProvider->query))]),
					'data' => [
						'method' => 'POST',
						'params' => [
							'leadsIds' => $searchModel->getAllIds($dataProvider->query),
						],
					],
				])
			: ''
		?>


	</div>


<?php endif; ?>

<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)): ?>
	<div class="btn-group">
		<?= Html::submitButton(
			'<i class="fa fa-tasks" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-warning',
				'name' => 'route',
				'value' => 'status/change',
				'title' => Yii::t('lead', 'Change Status'),
				'aria-label' => Yii::t('lead', 'Change Status'),
			])
		?>
		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)),

				['status/change'],
				[
					'class' => 'btn btn-warning',
					'data' => [
						'title' => Yii::t('lead', 'Change Status ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
						'aria-label' => Yii::t('lead', 'Change Status ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
						'method' => 'POST',
						'params' => [
							'leadsIds' => $searchModel->getAllIds($dataProvider->query),
						],
					],
				])
			: ''
		?>
	</div>

<?php endif; ?>

<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)): ?>
	<div class="btn-group">
		<?= Html::submitButton(
			'<i class="fa fa-feed" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-warning',
				'name' => 'route',
				'value' => 'source/change',
				'title' => Yii::t('lead', 'Change Source'),
				'aria-label' => Yii::t('lead', 'Change Source'),
			])
		?>
		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)),

				['source/change'],
				[
					'class' => 'btn btn-warning',
					'data' => [
						'title' => Yii::t('lead', 'Change Source ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
						'aria-label' => Yii::t('lead', 'Change Source ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
						'method' => 'POST',
						'params' => [
							'leadsIds' => $searchModel->getAllIds($dataProvider->query),
						],
					],
				])
			: ''
		?>
	</div>

<?php endif; ?>

<?php if (Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)): ?>
	<div class="btn-group">
		<?= Html::submitButton(
			'<i class="fa fa-envelope" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-primary',
				'name' => 'route',
				'value' => 'sms/push-multiple',
			])

		?>

		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)), [
				'sms/push-multiple',
			],
				count($searchModel->getAllIds($dataProvider->query)) < 6000
					? [
					'data' => [
						'method' => 'POST',
						'params' => [
							'leadsIds' => $searchModel->getAllIds($dataProvider->query),
						],
					],
					'class' => 'btn btn-primary',
					'title' => Yii::t('lead', 'Send SMS: {count}', [
						'count' => count($searchModel->getAllIds($dataProvider->query)),
					]),
					'aria-label' => Yii::t('lead', 'Send SMS: {count}', [
						'count' => count($searchModel->getAllIds($dataProvider->query)),
					]),
				]
					: [
					'disabled' => 'disabled',
					'title' => Yii::t('lead', 'For Send SMS records must be less then 6000.'),
					'aria-label' => Yii::t('lead', 'For Send SMS records must be less then 6000.'),
					'class' => 'btn btn-primary disabled',
				]
			)
			: ''
		?>
	</div>
<?php endif; ?>


<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER)): ?>
	<div class="btn-group">

		<?= Html::submitButton(
			'<i class="fa fa-phone" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-primary',
				'name' => 'route',
				'value' => 'dialer/create-multiple',
				'title' => Yii::t('lead', 'Assign to Dialer'),
				'aria-label' => Yii::t('lead', 'Assign to Dialer'),
			])
		?>

		<?= $dataProvider->pagination->pageCount > 1

			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)),
				['dialer/create-multiple'],
				[
					'class' => 'btn btn-primary',
					'title' => Yii::t('lead', 'Assign to Dialer ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
					'aria-label' => Yii::t('lead', 'Assign to Dialer ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),

					'data' => [
						'method' => 'POST',
						'params' => [
							'leadsIds' => $searchModel->getAllIds($dataProvider->query),
						],
					],
				])
			: ''
		?>
	</div>

<?php endif; ?>


<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)): ?>
	<div class="btn-group">
		<?= Html::submitButton(
			'<i class="fa fa-bullhorn" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-success',
				'name' => 'route',
				'value' => 'market/create-multiple',
				'title' => Yii::t('lead', 'Move to Market'),
				'aria-label' => Yii::t('lead', 'Move to Market'),
			])
		?>

		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)),
				['market/create-multiple'],
				[
					'class' => 'btn btn-success',
					'title' => Yii::t('lead', 'Move to Market ({count})', ['count' => count($searchModel->getAllIds($dataProvider->query))]),
					'aria-label' => Yii::t('lead', 'Move to Market ({count})', ['count' => count($searchModel->getAllIds($dataProvider->query))]),
					'data' => [
						'method' => 'POST',
						'params' => [
							'leadsIds' => $searchModel->getAllIds($dataProvider->query),
						],
					],
				])
			: ''
		?>
	</div>
<?php endif; ?>

<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_DELETE)): ?>

	<div class="btn-group pull-right">
		<?= Html::a(
			'<i class="fa fa-trash" aria-hidden="true"></i>',
			['delete-multiple', 'ids' => $dataProvider->getKeys()],
			[
				'class' => 'btn btn-danger',
				'title' => Yii::t('lead', 'Delete'),
				'aria-label' => Yii::t('lead', 'Delete'),
				'data' => [
					'method' => 'POST',
					'confirm' => Yii::t('lead', 'Are you sure you want to delete this items?'),
				],
			])
		?>

		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				count($searchModel->getAllIds($dataProvider->query)),
				['lead/delete-multiple', 'ids' => $searchModel->getAllIds($dataProvider->query)],
				[
					'class' => 'btn btn-danger',
					'title' => Yii::t('lead', 'Delete ({count})', [
						'count' => $dataProvider->getTotalCount(),
					]),
					'aria-label' => Yii::t('lead', 'Delete ({count})', [
						'count' => $dataProvider->getTotalCount(),
					]),
					'data' => [
						'method' => 'POST',
						'confirm' => Yii::t('lead', 'Are you sure you want to delete this items?'),
					],
				])
			: ''
		?>

	</div>

<?php endif; ?>

<?= Yii::$app->user->can(Worker::PERMISSION_EXPORT)
	? CsvForm::widget([
		'buttonText' => Html::icon('export'),
		'endForm' => false,
		'buttonOptions' => [
			'class' => 'btn btn-secondary not-selected-all',
		],
	])
	: ''
?>

