<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\provision\models\ProvisionUserData;
use backend\widgets\GridView;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ProvisionUserData */
/* @var $selfDataProvider ActiveDataProvider */
/* @var $fromDataProvider ActiveDataProvider */
/* @var $toDataProvider ActiveDataProvider */
/* @var $parentsWithoutProvisionsDataProvider ActiveDataProvider|null */
/* @var $allChildesDataProvider ActiveDataProvider|null */

$this->title = $model->hasType()
	? Yii::t('provision', 'Schemas provisions: {type} for {user}', [
		'type' => $model->type->name, 'user' => $model->getUser()->getFullName(),
	])
	: Yii::t('provision', 'Schemas provisions: {user}', ['user' => $model->getUser()->getFullName()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['type/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Schemas provisions'), 'url' => ['index']];
if ($model->hasType()) {
	$this->params['breadcrumbs'][] = ['label' => $model->getUser()->getFullName(), 'url' => ['user-view', 'userId' => $model->getUser()->id]];
	$this->params['breadcrumbs'][] = $model->type->name;
} else {
	$this->params['breadcrumbs'][] = $model->getUser()->getFullName();
}

?>
<div class="provision-user-type">

	<p>
		<?= Html::a(
			Yii::t('backend', 'Assign supervisor'),
			['/user/worker/hierarchy', 'id' => $model->getUser()->id],
			['class' => 'btn btn-success'])
		?>
		<?= $model->hasType() ? Html::a(
			Yii::t('provision', 'Update provision type'),
			['type/update', 'id' => $model->getTypeId()], [
			'class' => 'btn btn-info',
		])
			: ''
		?>
	</p>

	<?= $model->hasType()
		? DetailView::widget([
			'model' => $model->type,
			'attributes' => [
				'formattedValue',
				'issueUserTypeName',
				'issueTypesNames',
				'calculationTypesNames',
				'withHierarchy:boolean',
			],
		])
		: ''
	?>


	<div class="row">
		<?php if (!$model->hasType() && !empty($model->getTypesNotSet())): ?>
			<?= GridView::widget([
				'dataProvider' => new ArrayDataProvider(
					['allModels' => $model->getTypesNotSet()]
				),
				'options' => [
					'class' => 'col-md-6',
				],
				'toolbar' => false,
				'panel' => [
					'type' => GridView::TYPE_WARNING,
					'before' => false,
					'heading' => '<i class="fa fa-usd"></i> ' . Yii::t('provision', 'Types not set.'),
					'after' => false,
					'footer' => false,
				],
				'columns' => [
					[
						'attribute' => 'name',
					],
					'formattedValue',
					'from_at:date',
					'to_at:date',
					[
						'class' => ActionColumn::class,
						'template' => '{create-self}',
						'buttons' => [
							'create-self' => static function (string $url, ProvisionType $data) use ($model): string {
								return Html::a(Html::icon('plus'), ['create-self', 'userId' => $model->getUser()->id, 'typeId' => $data->id]);
							},
						],
					],
				],
			]) ?>
		<?php endif; ?>

		<?= GridView::widget([
			'dataProvider' => $selfDataProvider,
			'options' => [
				'class' => 'col-md-6',
			],
			'toolbar' => [
				[
					'content' =>
						Html::a(Html::icon('plus'), ['create-self', 'typeId' => $model->getTypeId(), 'userId' => $model->getUser()->id], [
							'class' => 'btn btn-success',
							'title' => Yii::t('provision', 'Create self provision'),
						]),
				],
			],
			'panel' => [
				'type' => GridView::TYPE_SUCCESS,

				'heading' => '<i class="fa fa-usd"></i> ' . Yii::t('provision', 'Self provisions'),
				'after' => false,
				'footer' => false,
			],
			'columns' => [
				[
					'attribute' => 'type.name',
					'visible' => !$model->hasType(),
				],
				'formattedValue',
				'from_at:date',
				'to_at:date',
				[
					'class' => ActionColumn::class,
					'buttons' => [
						'view' => static function (string $url, ProvisionUser $data) use ($model): string {
							if (!$model->hasType()) {
								return Html::a(Html::icon('eye-open'), \backend\helpers\Url::userProvisions($data->to_user_id, $data->type_id));
							}
							return '';
						},
					],
				],
			],
		]) ?>

		<div class="col-md-3">

			<?= $parentsWithoutProvisionsDataProvider && $parentsWithoutProvisionsDataProvider->getTotalCount()
				? GridView::widget([
					'dataProvider' => $parentsWithoutProvisionsDataProvider,
					'panel' => [
						'type' => GridView::TYPE_WARNING,
						'before' => false,
						'heading' => '<i class="fa fa-users"></i> ' . Yii::t('provision', 'Parents without provisions'),
						'after' => false,
						'footer' => false,
					],
					'columns' => [
						[
							'label' => Yii::t('provision', 'User'),
							'format' => 'html',
							'value' => static function (User $user) use ($model): string {
								return Html::a($user->getFullName(), Url::userProvisions($user->id, $model->getTypeId()));
							},
						],
						[
							'class' => ActionColumn::class,
							'template' => '{create}',
							'buttons' => [
								'create' => static function (string $url, $data, $id) use ($model): string {
									return Html::a(Html::icon('plus'), [
										'create', 'typeId' => $model->getTypeId(), 'fromUserId' => $model->getUser()->id, 'toUserId' => $id,
									]);
								},
							],
						],
					],
				])
				: ''
			?>

		</div>

		<div class="col-md-3">

			<?= $allChildesDataProvider && $allChildesDataProvider->getTotalCount()
				? GridView::widget([
					'dataProvider' => $allChildesDataProvider,
					'panel' => [
						'type' => GridView::TYPE_WARNING,
						'before' => false,
						'heading' => '<i class="fa fa-users"></i> ' . Yii::t('provision', 'Subordinates without provisions'),
						'after' => false,
						'footer' => false,
					],
					'columns' => [
						[
							'label' => Yii::t('provision', 'User'),
							'format' => 'html',
							'value' => static function (User $user) use ($model): string {
								return Html::a($user->getFullName(), Url::userProvisions($user->id, $model->getTypeId()));
							},
						], [
							'class' => ActionColumn::class,
							'template' => '{create}',
							'buttons' => [
								'create' => static function (string $url, $data, $id) use ($model): string {
									return Html::a(Html::icon('plus'), [
										'create', 'typeId' => $model->getTypeId(), 'fromUserId' => $id, 'toUserId' => $model->getUser()->id,
									]);
								},
							],
						],
					],
				])
				: ''
			?>

		</div>
	</div>


	<div class="row">

		<?= GridView::widget([
			'dataProvider' => $toDataProvider,
			'options' => [
				'class' => 'col-md-6',
			],
			'toolbar' => [
				[
					'content' =>
						Html::a(Html::icon('plus'), ['create', 'typeId' => $model->getTypeId(), 'fromUserId' => $model->getUser()->id], [
							'class' => 'btn btn-success',
							'title' => Yii::t('provision', 'Create to provision'),
						]),
				],
			],
			'panel' => [
				'heading' => '<i class="fa fa-usd"></i> ' . Yii::t('provision', 'Parent provisions'),
				'after' => false,
				'footer' => false,
			],
			'columns' => [
				[
					'attribute' => 'type.name',
					'visible' => !$model->hasType(),
				],
				[
					'value' => static function (ProvisionUser $provisionUser): string {
						return Html::a($provisionUser->toUser, ['user-view', 'userId' => $provisionUser->to_user_id, 'typeId' => $provisionUser->type_id]);
					},
					'format' => 'html',
					'label' => Yii::t('provision', 'User'),
				],
				'formattedValue',
				'from_at:date',
				'to_at:date',
				[
					'class' => ActionColumn::class,
					'buttons' => [
						'view' => static function (string $url, ProvisionUser $data): string {
							return Html::a(Html::icon('eye-open'), Url::userProvisions($data->to_user_id, $data->type_id));
						},
					],
				],
			],
		]) ?>

		<?= GridView::widget([
			'dataProvider' => $fromDataProvider,
			'options' => [
				'class' => 'col-md-6',
			],
			'toolbar' => [
				[
					'content' =>
						Html::a(Html::icon('plus'), ['create', 'typeId' => $model->getTypeId(), 'toUserId' => $model->getUser()->id], [
							'class' => 'btn btn-success',
							'title' => Yii::t('provision', 'Create from provision'),
						]),
				],
			],
			'panel' => [
				'heading' => '<i class="fa fa-usd"></i> ' . Yii::t('provision', 'Over provisions'),
				'after' => false,
				'footer' => false,
			],
			'columns' => [
				[
					'attribute' => 'type.name',
					'visible' => !$model->hasType(),
				],
				[
					'value' => static function (ProvisionUser $provisionUser): string {
						return Html::a($provisionUser->fromUser, ['user-view', 'userId' => $provisionUser->from_user_id, 'typeId' => $provisionUser->type_id]);
					},
					'format' => 'html',
					'label' => Yii::t('provision', 'User'),
				],
				'formattedValue',
				'from_at:date',
				'to_at:date',
				[
					'class' => ActionColumn::class,
					'buttons' => [
						'view' => static function (string $url, ProvisionUser $data): string {
							return Html::a(Html::icon('eye-open'), Url::userProvisions($data->from_user_id, $data->type_id));
						},
					],
				],
			],
		]) ?>

	</div>


</div>
