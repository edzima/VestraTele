<?php

use backend\modules\issue\models\search\IssueNoteSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel IssueNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notatki';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'issue_id',
				'value' => 'issue',
				'label' => 'Sprawa',
			],
			'user',
			'title',
			'description',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
