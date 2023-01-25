<?php

use backend\helpers\Html;
use backend\modules\user\models\search\UserVisibleSearch;
use backend\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel UserVisibleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'User Visibles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-visible-index">


	<p>
		<?= Html::a(Yii::t('backend', 'Create User Visible'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => UserVisibleSearch::getUsersNames(),

			],
			[
				'attribute' => 'to_user_id',
				'value' => 'toUser',
				'filter' => UserVisibleSearch::getToUsersNames(),
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => UserVisibleSearch::getStatusesNames(),
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
