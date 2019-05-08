<?php

use common\models\issue\IssuePay;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\issue\IssuePaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wpłaty';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'issue_id',
				'format' => 'raw',
				'value' => function (IssuePay $model) {
					return Html::a(
						$model->issue,
						['issue/view', 'id' => $model->issue_id],
						['target' => '_blank']);
				},
			],
			'date:date',
			'value:decimal',

			[
				'class' => ActionColumn::class,
				'template' => '{update} {delete}',
			],
		],
	]); ?>
</div>
