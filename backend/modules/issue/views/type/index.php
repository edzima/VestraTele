<?php

use backend\modules\issue\models\search\IssueTypeSearch;
use common\helpers\ArrayHelper;
use common\models\issue\IssueType;
use common\models\user\Worker;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssueTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Issue Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-index">
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(Yii::t('backend', 'Create issue type'), ['create'], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('issue', 'Stages'), ['stage/index'], ['class' => 'btn btn-info']) ?>

	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'name',
			'short_name',
			'is_main:boolean',
			'vat',
			'with_additional_date:boolean',
			[
				'attribute' => 'parent_id',
				'value' => 'parentName',
				'filter' => ArrayHelper::map(IssueTypeSearch::getParents(), 'id', 'name'),
			],
			[
				'attribute' => 'roles',
				'value' => function (IssueType $model): ?string {
					$items = Yii::$app->issueTypeUser->getParentsRoles($model->id);
					$names = [];
					foreach ($items as $item) {
						$names[] = Worker::getRolesNames()[$item];
					}
					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
				'label' => Yii::t('backend', 'Roles'),
				'visible' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_TYPE_PERMISSIONS),
			],
			[
				'attribute' => 'permissions',
				'value' => function (IssueType $model): ?string {
					$items = Yii::$app->issueTypeUser->getParentsPermissions($model->id);
					$names = [];
					foreach ($items as $item) {
						$names[] = Worker::getPermissionsNames()[$item];
					}
					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
				'visible' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_TYPE_PERMISSIONS),
				'label' => Yii::t('backend', 'Permissions'),
			],
			'lead_source_id',
			'default_show_linked_notes:boolean',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
