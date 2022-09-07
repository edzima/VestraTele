<?php

use backend\modules\issue\models\search\ClaimSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\widgets\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel ClaimSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Issue Claims');

$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-claim-index">


	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			[
				'class' => IssueColumn::class,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => ClaimSearch::getTypesNames(),
			],
			[
				'attribute' => 'entity_responsible_id',
				'value' => 'entityResponsible.name',
				'filter' => ClaimSearch::getEntityResponsibleNames(),
			],
			'trying_value:currency',
			'obtained_value:currency',
			'percent_value',
			'details:ntext',
			'date:date',
			[
				'class' => ActionColumn::class,
				'template' => '{update} {delete}',
			],
		],
	]); ?>


</div>
