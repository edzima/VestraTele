<?php

use common\widgets\grid\ActionColumn;
use frontend\helpers\Html;
use frontend\models\search\PotentialClientSearch;
use frontend\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel PotentialClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'My Potential Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="potential-client-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('common', 'Search'), ['search'], ['class' => 'btn btn-info']) ?>

		<?= Html::a(Yii::t('common', 'Create Potential Client'), ['create'], ['class' => 'btn btn-success']) ?>

	</p>

	<?= $this->render('_search', [
		'model' => $searchModel,
		'withAddress' => true,
		'withFirstname' => true,
		'withLastname' => true,
		'action' => 'self',
	]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => PotentialClientSearch::getStatusesNames(),
			],
			'firstname',
			'lastname',
			'birthday',
			'details:ntext',
			'cityName',
			'created_at',
			'updated_at:date',
			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>
