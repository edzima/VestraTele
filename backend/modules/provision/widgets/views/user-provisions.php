<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\provision\models\ProvisionUserData;
use backend\modules\provision\widgets\UserProvisionsWidget;
use backend\widgets\GridView;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\User;
use common\widgets\FieldsetDetailView;
use common\widgets\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $model ProvisionUserData */
/* @var $selfDataProvider ActiveDataProvider */
/* @var $fromDataProvider ActiveDataProvider|null */
/* @var $toDataProvider ActiveDataProvider */
/* @var $parentsWithoutProvisionsDataProvider ActiveDataProvider|null */
/* @var $allChildesDataProvider ActiveDataProvider|null */
/* @var $extraProvisionsColumns array */
/* @var UserProvisionsWidget $context */
$context = $this->context;
?>
<div class="user-provisions">


	<?php if ($model->hasType() && $context->withTypeDetail): ?>
		<p>
			<?= Html::a(
				Yii::t('provision', 'Update provision type'),
				['/provision/type/update', 'id' => $model->getTypeId()], [
				'class' => 'btn btn-info',
			]) ?>
		</p>

		<?= FieldsetDetailView::widget([
			'legend' => $model->type->name,
			'detailConfig' => [
				'model' => $model->type,
				'attributes' => [
					'formattedValue',
					'issueUserTypeName',
					'issueTypesNames',
					'calculationTypesNames',
					'withHierarchy:boolean',
				],
			],
		]) ?>

	<?php endif; ?>


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
						'template' => '{create-self} {view}',
						'buttons' => [
							'create-self' => function (string $url, ProvisionType $data) use ($context): string {
								return Html::a(Html::icon('plus'),
									$context->getCreateSelfUrl($data->id)
								);
							},
							'view' => static function (string $url, ProvisionType $data) use ($model): string {
								return Html::a(Html::icon('eye-open'), Url::userProvisions($model->getUser()->id, $data->id));
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
						Html::a(Html::icon('plus'),
							$context->getCreateSelfUrl($model->getTypeId()), [
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
			'columns' => array_merge($extraProvisionsColumns, [
				[
					'attribute' => 'type.name',
					'visible' => !$model->hasType(),
				],//@todo only percent type
				'formattedValue',
				'from_at:date',
				'to_at:date',
				[
					'class' => ActionColumn::class,
					'controller' => '/provision/user',
					'buttons' => [
						'view' => static function (string $url, ProvisionUser $data) use ($model): string {
							return Html::a(Html::icon('eye-open'), Url::userProvisions($data->to_user_id, $data->type_id));
						},
					],
				],
			]),
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
										'/provision/user/create', 'typeId' => $model->getTypeId(), 'fromUserId' => $model->getUser()->id, 'toUserId' => $id,
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
						Html::a(Html::icon('plus'),
							['/provision/user/create', 'typeId' => $model->getTypeId(), 'fromUserId' => $model->getUser()->id], [
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
			'columns' => array_merge($extraProvisionsColumns, [
				[
					'attribute' => 'type.name',
					'visible' => !$model->hasType(),
				],
				'toUser',
				'formattedValue',
				'from_at:date',
				'to_at:date',
				[
					'class' => ActionColumn::class,
					'controller' => '/provision/user',
					'buttons' => [
						'view' => static function (string $url, ProvisionUser $data): string {
							return Html::a(Html::icon('eye-open'), Url::userProvisions($data->to_user_id, $data->type_id));
						},
					],
				],
			]),
		]) ?>

		<?= $fromDataProvider ? GridView::widget([
			'dataProvider' => $fromDataProvider,
			'options' => [
				'class' => 'col-md-6',
			],
			'toolbar' => [
				[
					'content' =>
						Html::a(Html::icon('plus'), ['/provision/user/create', 'typeId' => $model->getTypeId(), 'toUserId' => $model->getUser()->id], [
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
			'columns' => array_merge($extraProvisionsColumns, [
				[
					'attribute' => 'type.name',
					'visible' => !$model->hasType(),
				],
				'fromUser',
				'formattedValue',
				'from_at:date',
				'to_at:date',
				[
					'class' => ActionColumn::class,
					'controller' => '/provision/user',
					'buttons' => [
						'view' => static function (string $url, ProvisionUser $data): string {
							return Html::a(Html::icon('eye-open'), Url::userProvisions($data->from_user_id, $data->type_id));
						},
					],
				],
			]),
		]) : '' ?>

	</div>


</div>
