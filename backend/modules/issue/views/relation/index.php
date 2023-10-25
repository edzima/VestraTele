<?php

use backend\modules\issue\models\search\RelationSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel RelationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Issue Relations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-relation-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('issue', 'Create Issue Relation'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'issue',
			'issue2',
			'created_at:datetime',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
