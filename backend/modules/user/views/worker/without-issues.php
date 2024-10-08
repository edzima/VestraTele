<?php

use backend\helpers\Url;
use backend\modules\user\models\search\WorkersWithoutIssuesSearch;
use backend\widgets\GridView;
use common\models\user\UserProfile;
use common\models\user\Worker;
use common\widgets\ButtonDropdown;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\SelectionForm;
use kartik\grid\CheckboxColumn;

/* @var $this yii\web\View */
/* @var $searchModel WorkersWithoutIssuesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Workers without Issues');
$this->params['breadcrumbs'][] = ['url' => 'index', 'label' => Yii::t('backend', 'Workers')];
$this->params['breadcrumbs'][] = $this->title;

$statuses = $searchModel::getStatusesNames();
$statusesItems = [];
foreach ($statuses as $statusId => $statusName) {
	if ($statusId != $searchModel->status) {
		$statusesItems[] = [
			'label' => $statusName,
			'url' => [
				'change-status',
				'status' => $statusId,
				'returnUrl' => Url::current(),
			],
			'linkOptions' => [
				'data-method' => 'POST',
				'data-confirm' => Yii::t('backend', 'Are you sure you want to change the status to:{status} for this workers?', [
					'status' => $statusName,
				]),
			],
		];
	}
}
?>
<div class="issue-user-workers-without-issues">

	<p>
		<?= $this->render('_without_issues_search', [
			'model' => $searchModel,
		]) ?>
	</p>

	<div class="grid-before">
		<?php
		SelectionForm::begin([
			'formWrapperSelector' => '.selection-form-wrapper',
			'gridId' => 'without-issues-grid',
		]);
		?>

		<div class="selection-form-wrapper hidden">

			<?= ButtonDropdown::widget([
				'label' => Yii::t('backend', 'Change Status'),
				'dropdown' => [
					'items' => $statusesItems,
				],
				'tagName' => 'a',
				'options' => [
					'class' => 'btn btn-warning',
				],
			]) ?>


		</div>
	</div>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'id' => 'without-issues-grid',
		'columns' => [
			[
				'class' => CheckboxColumn::class,
			],
			[
				'attribute' => 'firstname',
				'value' => 'profile.firstname',
				'label' => UserProfile::instance()->getAttributeLabel('firstname'),
			],
			[
				'attribute' => 'lastname',
				'value' => 'profile.lastname',
				'label' => UserProfile::instance()->getAttributeLabel('lastname'),
			],
			'email:email',
			[
				'attribute' => 'phone',
				'value' => 'profile.phone',
				'label' => UserProfile::instance()->getAttributeLabel('phone'),
				'format' => 'tel',
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => WorkersWithoutIssuesSearch::getStatusesNames(),
				'visible' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),

			],
			[
				'attribute' => 'ip',
				'visible' => Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR),
			],
			'created_at:datetime',
			'action_at:Datetime',
			[
				'class' => ActionColumn::class,
				'controller' => '/user/worker',
				'template' => '{view} {update} {delete}',
				'visibleButtons' => [
					'view' => true,
					'update' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),
					'delete' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),
				],

			],
		],
	]) ?>

	<?php SelectionForm::end() ?>


</div>
