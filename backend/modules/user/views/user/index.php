<?php

use backend\helpers\Html;
use backend\modules\user\models\search\UserSearch;
use backend\widgets\GridView;
use common\models\user\UserProfile;
use common\widgets\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Users');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create user'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('common', 'User Traits'), ['trait/index'], ['class' => 'btn btn-warning']) ?>
	</p>

	<?= $this->render('_search', [
		'model' => $searchModel,
	]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],
			'id',
			'username',
			'email:email',
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
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => UserSearch::getStatusesNames(),
			],
			'ip',
			[
				'attribute' => 'gender',
				'value' => 'profile.genderName',
				'filter' => UserProfile::getGendersNames(),
			],
			'created_at:datetime',
			'updated_at:datetime',
			'action_at:Datetime',
			[
				'class' => ActionColumn::class,
			],
		],
	]) ?>

</div>
