<?php

use backend\helpers\Url;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Users');
$this->params['breadcrumbs'][] = $this->title;

$typWok = ['0' => 'Przedstawiciel', '1' => 'Telemarketer'];
?>
<div class="user-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create user'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('db_rbac', 'Role'), ['/rbac/access/role'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('db_rbac', 'Permissions'), ['/rbac/access/permission'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],
			'id',
			'username',
			'boss',
			// 'auth_key',
			// 'access_token',
			// 'password_hash',
			'email:email',
			[
				'attribute' => 'status',
				'value' => function ($model) {
					return User::statuses($model->status);
				},
				'filter' => User::statuses(),
			],
			'ip',
			// 'created_at',
			// 'updated_at',
			'action_at:Datetime',

			[
				'class' => ActionColumn::class,
				'template' => '{update} {provision} {delete}',
				'buttons' => [
					'provision' => static function (string $url, User $model) {
						return Html::a('<span class="glyphicon glyphicon-usd"></span>',
							Url::userProvisions($model->id),
							[
								'title' => 'Podgląd',
								'aria-label' => 'Podgląd',
								'data-pjax' => '0',
							]);
					},

				],

			],
		],
	]) ?>

</div>
