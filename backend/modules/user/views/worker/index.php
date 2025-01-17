<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\user\models\search\WorkerUserSearch;
use backend\modules\user\widgets\CopyToCliboardFormAttributesBtn;
use backend\widgets\GridView;
use common\models\user\PasswordResetRequestForm;
use common\models\user\UserProfile;
use common\models\user\Worker;
use common\modules\court\modules\spi\Module;
use common\widgets\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel WorkerUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Workers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-customer-index">

	<p>

		<?= Yii::$app->user->can(Module::PERMISSION_SPI_USER_AUTH)
			? Html::a(Html::faicon('legal'), ['/court/spi/user-auth/index'], ['class' => 'btn btn-warning'])
			: ''
		?>

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
				'template' => '{request-password-reset} {spi-user-auth} {copy} {view} {update} {provision} {hierarchy} {delete}',
				'buttons' => [
					'spi-user-auth' => static function (string $url, Worker $model) {
						return Html::a(Html::faicon('legal'),
							['/court/spi/user-auth/user', 'id' => $model->id],
							[
								'title' => Yii::t('spi', 'SPI Auth'),
								'aria-label' => Yii::t('spi', 'SPI Auth'),
								'data-pjax' => '0',
							]);
					},
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
					'request-password-reset' => static function (string $url, Worker $model): ?string {
						if (!PasswordResetRequestForm::forUser($model)) {
							return null;
						}
						return Html::a('<i class="fa fa-key"></i>',
							['request-password-reset', 'id' => $model->id, 'returnUrl' => Url::current()],
							[
								'title' => Yii::t('backend', 'Reset password'),
								'aria-label' => Yii::t('backend', 'Reset password'),
								'data-pjax' => '0',
								'data-method' => 'POST',
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
					'spi-user-auth' => Yii::$app->user->can(Module::PERMISSION_SPI_USER_AUTH),
					'request-password-reset' => Yii::$app->user->can(Worker::PERMISSION_WORKERS),
				],

			],
		],
	]) ?>

</div>
