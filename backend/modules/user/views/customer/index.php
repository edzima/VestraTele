<?php

use backend\modules\user\models\search\CustomerUserSearch;
use backend\widgets\GridView;
use common\models\user\UserProfile;
use kartik\grid\ActionColumn;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel CustomerUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Customers');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-user-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create customer'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			//['class' => 'yii\grid\SerialColumn'],
			'id',
			[
				'attribute' => 'firstname',
				'value' => 'profile.firstname',
			],
			[
				'attribute' => 'lastname',
				'value' => 'profile.lastname',
			],
			'email:email',
			'profile.phone',
			[
				'attribute' => 'gender',
				'value' => 'profile.genderName',
				'filter' => UserProfile::getGendersNames(),
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => CustomerUserSearch::getStatusesNames(),
			],
			'created_at:datetime',
			'action_at:datetime',
			'issueCount',
			[
				'class' => ActionColumn::class,
				'template' => '{update}{view}',
			],
		],
	]) ?>

</div>
