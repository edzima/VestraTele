<?php

use backend\widgets\GridView;
use common\models\provision\ProvisionUsersSearch;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel ProvisionUsersSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('provision', 'Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = $this->title;
$dateFrom = $searchModel->dateFrom;
$dateTo = $searchModel->dateTo;

$models = $dataProvider->getModels();

?>
<div class="provision-index">

	<?= $this->render('_new_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'showPageSummary' => true,
		'columns' => [
			'id',
			'total',
			/*
			[
				'label' => Yii::t('provision', 'User'),
				'attribute' => 'id',
				'value' => static function (User $user) use ($dateTo, $dateFrom): string {
					return Html::a($user->getFullName(), Url::to(['view', 'id' => $user->id, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]));
				},
				'format' => 'raw',
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'provision.value',
				'format' => 'currency',
				//		'pageSummary' => true,
			],
			*/
		],
	]) ?>


</div>
