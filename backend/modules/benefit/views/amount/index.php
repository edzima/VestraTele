<?php

use common\models\benefit\BenefitAmountSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel BenefitAmountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wielkości zasiłków';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="benefit-amount-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => BenefitAmountSearch::getTypesNames(),
			],
			'from_at:date',
			'to_at:date',
			'value:decimal',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
