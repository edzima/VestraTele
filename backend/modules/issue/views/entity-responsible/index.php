<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\issue\IssueEntityResponsibleSearch */
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
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
