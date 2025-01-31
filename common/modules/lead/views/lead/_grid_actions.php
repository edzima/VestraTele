<?php

use backend\widgets\CsvForm;
use common\helpers\Html;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\lead\controllers\LeadController;
use common\modules\lead\models\searches\LeadSearch;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $assignUsers bool */
$dataProvider->getModels();
$count = $dataProvider->getTotalCount();

$params = [
	LeadController::LEADS_SEARCH_QUERY_PARAM => Json::encode(Yii::$app->request->queryParams),
];
?>


<?= Html::beginForm() ?>


<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_UPDATE_MULTIPLE)): ?>
	<div class="btn-group">
		<?= Html::submitButton(
			'<i class="fa fa-pencil" aria-hidden="true"></i>',
			[
				'class' => 'btn btn-info',
				'name' => 'route',
				'value' => 'lead/update-multiple',
				'title' => Yii::t('lead', 'Update'),
				'aria-label' => Yii::t('lead', 'Update'),
			])
		?>
		<?= ($dataProvider->pagination->pageCount > 1)
			? Html::a(
				$count,
				['lead/update-multiple'],
				[
					'class' => 'btn btn-info',
					'data' => [
						'title' => Yii::t('lead', 'Update ({ids})', ['ids' => $count]),
						'aria-label' => Yii::t('lead', 'Update ({ids})', ['ids' => $count]),
						'method' => 'POST',
						'params' => $params,
					],
				])
			: ''
		?>
	</div>

<?php endif; ?>

<div class="btn-group">
	<?= Html::submitButton(
		Html::faicon('bullhorn'),
		[
			'class' => 'btn btn-warning',
			'name' => 'route',
			'value' => 'campaign/assign',
			'title' => Yii::t('lead', 'Campaign'),
			'aria-label' => Yii::t('lead', 'Campaign'),
		])
	?>
	<?= $dataProvider->pagination->pageCount > 1
		? Html::a(
			$count,
			['campaign/assign'],
			[
				'class' => 'btn btn-warning',
				'data' => [
					'title' => Yii::t('lead', 'Campaign ({ids})', ['ids' => $count]),
					'aria-label' => Yii::t('lead', 'Campaign ({ids})', ['ids' => $count]),
					'method' => 'POST',
					'params' => $params,
				],
			])
		: ''
	?>
</div>


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
				$count,
				['user/assign'],
				[
					'class' => 'btn btn-info',
					'title' => Yii::t('lead', 'Link Users ({count})', ['count' => $count]),
					'aria-label' => Yii::t('lead', 'Link Users ({count})', ['count' => $count]),
					'data' => [
						'method' => 'POST',
						'params' => $params,
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
				$count,
				['status/change'],
				[
					'class' => 'btn btn-warning',
					'title' => Yii::t('lead', 'Change Status ({ids})', ['ids' => $count]),
					'aria-label' => Yii::t('lead', 'Change Status ({ids})', ['ids' => $count]),
					'data' => [
						'method' => 'POST',
						'params' => $params,
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
				$count,
				['source/change'],
				[
					'class' => 'btn btn-warning',
					'title' => Yii::t('lead', 'Change Source ({ids})', ['ids' => $count]),
					'aria-label' => Yii::t('lead', 'Change Source ({ids})', ['ids' => $count]),
					'data' => [

						'method' => 'POST',
						'params' => $params,
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
				'title' => Yii::t('lead', 'Send SMS'),
				'aria-label' => Yii::t('lead', 'Send SMS'),
			])

		?>

		<?= $dataProvider->pagination->pageCount > 1
			? Html::a(
				$count, [
				'sms/push-multiple',
			],
				$count < 6000
					? [
					'data' => [
						'method' => 'POST',
						'params' => $params,
					],
					'class' => 'btn btn-primary',
					'title' => Yii::t('lead', 'Send SMS: {count}', [
						'count' => $count,
					]),
					'aria-label' => Yii::t('lead', 'Send SMS: {count}', [
						'count' => $count,
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
				$count,
				['dialer/create-multiple'],
				[
					'class' => 'btn btn-primary',
					'title' => Yii::t('lead', 'Assign to Dialer ({ids})', ['ids' => $count]),
					'aria-label' => Yii::t('lead', 'Assign to Dialer ({ids})', ['ids' => $count]),

					'data' => [
						'method' => 'POST',
						'params' => $params,
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
				$count,
				['market/create-multiple'],
				[
					'class' => 'btn btn-success',
					'title' => Yii::t('lead', 'Move to Market ({count})', ['count' => $count]),
					'aria-label' => Yii::t('lead', 'Move to Market ({count})', ['count' => $count]),
					'data' => [
						'method' => 'POST',
						'params' => $params,
					],
				])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_EXPORT) && !empty(Yii::$app->request->getQueryString())
			? Html::a(Html::icon('export') . '<sub><i class="fa fa-sitemap"></i></sub>', [
				'export/query-grouped-by-types', 'query' => Yii::$app->request->getQueryString(),
			], [
				'data-method' => 'POST',
				'class' => 'btn btn-secondary not-selected-all',
				'data-pjax' => 0,
				'title' => Yii::t('lead', 'Export grouped by Types'),
				'aria-label' => Yii::t('lead', 'Export grouped by Types'),
			])
			: '' ?>
	</div>
<?php endif; ?>

<?php if (Yii::$app->user->can(User::PERMISSION_LEAD_DELETE)): ?>

	<div class="btn-group pull-right">
		<?= Html::a(
			'<i class="fa fa-trash" aria-hidden="true"></i>',
			['delete-multiple'],
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
				$count,
				['lead/delete-multiple'],
				[
					'class' => 'btn btn-danger',
					'title' => Yii::t('lead', 'Delete ({count})', [
						'count' => $count,
					]),
					'aria-label' => Yii::t('lead', 'Delete ({count})', [
						'count' => $count,
					]),
					'data' => [
						'method' => 'POST',
						'params' => $params,
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

