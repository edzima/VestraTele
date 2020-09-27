<?php

use backend\modules\user\models\search\CustomerUserSearch;
use backend\widgets\GridView;
use edzima\teryt\models\Region;
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
			[
				'attribute' => 'firstname',
				'value' => 'profile.firstname',
			],
			[
				'attribute' => 'lastname',
				'value' => 'profile.lastname',
			],
			[
				'attribute' => 'region_id',
				'value' => 'homeAddress.city.region',
				'label' => Yii::t('address', 'Region'),
				'filter' => Region::getNames(),
			],
			[
				'attribute' => 'city_id',
				'value' => 'homeAddress.city.name',
				'label' => Yii::t('address', 'City'),
			],
			'email:email',
			'profile.phone',
			'updated_at:datetime',

			'issuesCount',
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update}',
			],
		],
	]) ?>

</div>
