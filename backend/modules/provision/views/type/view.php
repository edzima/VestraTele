<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\widgets\GridView;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueProvisionType */
/* @var $userWithTypes ActiveDataProvider */
/* @var $withoutType ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="provision-type-view">

	<p>
		<?= Html::a(Yii::t('provision', 'Create provision schema'), ['user/create', 'typeId' => $model->id], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'name',
			'is_active:boolean',
			'formattedValue',
			'issueUserTypeName',
			'issueRequiredUserTypesNames',
			'issueTypesNames',
			'issueStagesNames',
			'settlementTypesNames',
			'withHierarchy:boolean',
			'from_at:date',
			'to_at:date',
		],
	]) ?>

	<div class="row">

		<div class="col-md-8">
			<?= GridView::widget([
				'caption' => Yii::t('provision', 'With set type'),
				'dataProvider' => $userWithTypes,
				'columns' => [
					'fromUserNameWhenNotSelf',
					'toUser',
					'formattedValue',
					'from_at:date',
					'to_at:date',
					[
						'class' => ActionColumn::class,
						'controller' => '/provision/user',
						'template' => '{view} {update} {delete}',
						'buttons' => [
							'view' => static function ($url, ProvisionUser $user): string {
								return Html::a(
									Html::icon('eye-open'),
									Url::userProvisions($user->to_user_id, $user->type_id),
								);
							},
						],
					],
				],

			]) ?>
		</div>

		<div class="col-md-4">
			<?= GridView::widget([
				'dataProvider' => $withoutType,
				'caption' => Yii::t('provision', 'Without set type'),
				'columns' => [
					'fullName',
					[
						'class' => ActionColumn::class,
						'template' => '{set} {view}',
						'buttons' => [
							'set' => static function ($url, User $user) use ($model) {
								return Html::a(
									Html::icon('plus'),
									['user/create-self', 'userId' => $user->id, 'typeId' => $model->id]);
							},
							'view' => static function ($url, User $user) use ($model) {
								return Html::a(
									Html::icon('eye-open'),
									Url::userProvisions($user->id, $model->id)
								);
							},
						],
					],
				],
			]) ?>
		</div>

	</div>


</div>
