<?php

use backend\helpers\Url;
use backend\modules\user\models\search\WorkerUserSearch;
use backend\widgets\GridView;
use common\models\user\UserProfile;
use common\models\user\Worker;
use kartik\grid\ActionColumn;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel WorkerUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Workers');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-customer-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create worker'), ['create'], ['class' => 'btn btn-success']) ?>
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
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => WorkerUserSearch::getStatusesNames(),
			],
			'ip',
			[
				'attribute' => 'gender',
				'value' => 'profile.genderName',
				'filter' => UserProfile::getGendersNames(),
			],
			'action_at:Datetime',
			[
				'class' => ActionColumn::class,
				'template' => '{update} {provision} {delete}',
				'buttons' => [
					'provision' => static function (string $url, Worker $model) {
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
