<?php

use common\models\entityResponsible\EntityResponsibleSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel EntityResponsibleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Podmioty odpowiedzialne';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-entity-responsible-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'name',
			'is_for_summon:boolean',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
