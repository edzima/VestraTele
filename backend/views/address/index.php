<?php

use backend\widgets\GridView;
use common\models\Address;
use common\models\AddressSearch;
use edzima\teryt\models\Region;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Addresses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'postal_code',
			[
				'attribute' => 'city_name',
				'value' => 'city.name',
				'label' => Yii::t('common', 'City'),
			],
			'info',
			[
				'attribute' => 'region_id',
				'label' => Yii::t('common', 'Region'),
				'filter' => Region::getNames(),
				'value' => 'city.region.name',
			],
			[
				'attribute' => 'userName',
				'value' => function (Address $model): string {
					$meetsNames = ArrayHelper::getColumn($model->meets, 'clientFullName');
					$usersNames = ArrayHelper::getColumn($model->users, 'fullName');
					$names = array_merge($meetsNames, $usersNames);
					if (!empty($names)) {
						return implode(', ', $names);
					}
					return '';
				},
			],
		],
	]) ?>

</div>
