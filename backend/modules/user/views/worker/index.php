<?php

use backend\helpers\Url;
use backend\modules\user\models\search\WorkerUserSearch;
use backend\modules\user\widgets\CopyToCliboardFormAttributesBtn;
use backend\widgets\GridView;
use common\models\user\UserProfile;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel WorkerUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Workers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-customer-index">

	<p>
		<?= Yii::$app->user->can(Worker::PERMISSION_WORKERS)
			? Html::a(Yii::t('backend', 'Create worker'), ['create'], ['class' => 'btn btn-success'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_WORKERS)
			? Html::a(Html::icon('paste'), ['create-from-json'], [
				'class' => 'btn btn-success',
				'title' => Yii::t('backend', 'Create worker from JSON'),
			])
			: ''
		?>
	</p>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
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
				'filter' => WorkerUserSearch::getStatusesNames(),
				'visible' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),

			],
			[
				'attribute' => 'ip',
				'visible' => Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR),
			],
			[
				'attribute' => 'gender',
				'value' => 'profile.genderName',
				'filter' => UserProfile::getGendersNames(),
			],
			'created_at:datetime',
			'action_at:Datetime',
			[
				'class' => ActionColumn::class,
				'template' => '{copy} {view} {update} {provision} {hierarchy} {delete}',
				'buttons' => [
					'copy' => static function (string $url, Worker $model): string {
						return CopyToCliboardFormAttributesBtn::widget([
							'model' => $model,
							'options' => [],
							'tag' => 'a',
						]);
					},
					'hierarchy' => static function (string $url, Worker $model) {
						return Html::a('<span class="glyphicon glyphicon-king"></span>',
							['hierarchy', 'id' => $model->id],
							[
								'title' => Yii::t('common', 'Hierarchy'),
								'aria-label' => Yii::t('common', 'Hierarchy'),
								'data-pjax' => '0',
							]);
					},
					'link' => static function (string $url, Worker $model) {
						return Html::a('<span class="glyphicon glyphicon-paperclip"></span>',
							['/issue/user/link', 'userId' => $model->id],
							[
								'title' => Yii::t('backend', 'Link to issue'),
								'aria-label' => Yii::t('backend', 'Link to issue'),
								'data-pjax' => '0',
							]);
					},
					'provision' => static function (string $url, Worker $model) {
						return Html::a('<i class="fa fa-percent"></i>',
							Url::userProvisions($model->id),
							[
								'title' => Yii::t('backend', 'Provisions'),
								'aria-label' => Yii::t('backend', 'Provisions'),
								'data-pjax' => '0',
							]);
					},
				],
				'visibleButtons' => [
					'view' => true,
					'update' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),
					'delete' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),
					'hierarchy' => Yii::$app->user->can(Worker::PERMISSION_WORKERS_HIERARCHY),
					'link' => Yii::$app->user->can(Worker::PERMISSION_ISSUE),
					'provision' => Yii::$app->user->can(Worker::PERMISSION_PROVISION),
				],

			],
		],
	]) ?>

</div>
