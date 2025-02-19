<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use common\behaviors\IssueTypeParentIdAction;
use common\helpers\Url;
use common\models\issue\SummonType;
use common\models\user\Worker;
use common\widgets\ButtonDropdown;
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

		<div class="selection-form-wrapper hidden">

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

					<?php
					$selectionItems = [];
					foreach (SummonType::getNames() as $id => $name) {
						$selectionItems[] = [
							'label' => Html::submitButton(
								Html::encode($name),
								[
									'name' => 'route',
									'value' => Url::to(['summon/create-multiple', 'typeId' => $id]),
									'data-pjax' => '0',
								]),
							'encode' => false,
							'options' => [
								'class' => 'raw-button',
							],
						];
						$allItems[] = [
							'label' => $name,
							'url' => ['summon/create-multiple', 'typeId' => $id],
							'linkOptions' => [
								'data' => [
									'pjax' => '0',
									'method' => 'POST',
									'params' => $queryParams,
								],
							],
						];
					}

					?>

					<?= !empty($selectionItems)
						? ButtonDropdown::widget([
							'dropdown' => [
								'items' => $selectionItems,
								'options' => [
									'class' => 'dropdown-raw-buttons',
								],
							],
							'label' => Html::faicon('bolt'),
							'encodeLabel' => false,
							'options' => [
								'class' => 'btn btn-warning dropdown-raw-buttons',
							],
						])
						: ''
					?>


					<?= !empty($allItems && $dataProvider->pagination->pageCount > 1)
						? ButtonDropdown::widget([
							'dropdown' => [
								'items' => $allItems,
							],
							'label' => $count,
							'options' => [
								'class' => 'btn btn-warning',
								'title' => Yii::t('backend', 'Create Summons for Issues: {count}', [
									'count' => $count,
								]),
								'aria-label' => Yii::t('backend', 'Create Summons for Issues: {count}', [
									'count' => $count,
								]),
							],
						])
						: ''
					?>
			</span>

			<?php endif; ?>


		</div>
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
