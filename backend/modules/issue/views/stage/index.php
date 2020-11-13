<?php

use backend\modules\issue\models\search\IssueStageSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel IssueStageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Etapy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-stage-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'name',
			'short_name',
			[
				'attribute' => 'typesName',
				'label' => 'Rodzaje',
			],
			'days_reminder',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
