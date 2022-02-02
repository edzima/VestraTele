<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\SummonTypeSearch;
use backend\widgets\GridView;
use common\widgets\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel SummonTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Summon Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['/issue/summon/index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Create Summon Type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'name',
			'short_name',
			'title',
			'term',
			['class' => ActionColumn::class],
		],
	]); ?>


</div>
