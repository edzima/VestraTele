<?php

use common\models\settlement\search\CostTypeSearch;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CostTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('settlement', 'Cost Types');
$this->params['breadcrumbs'][] = ['url' => ['cost/index'], 'label' => Yii::t('settlement', 'Costs')];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cost-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('settlement', 'Create Cost Type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'name',
			'is_active:boolean',
			'is_for_settlement:boolean',
			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>
