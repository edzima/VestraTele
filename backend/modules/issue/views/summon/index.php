<?php

use backend\helpers\Url;
use backend\modules\issue\models\search\SummonSearch;
use common\models\issue\Summon;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SummonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Summons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Create Summon'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'issue_id',
				'value' => function (Summon $model): string {
					return Html::a($model->issue,
						Url::issueView($model->issue_id),
						[
							'target' => '_blank',
						]);
				},
				'label' => 'Sprawa',
				'format' => 'raw',
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => SummonSearch::getTypesNames(),
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => SummonSearch::getStatusesNames(),
			],
			[
				'attribute' => 'term',
				'value' => 'termName',
				'filter' => SummonSearch::getTermsNames(),
			],
			'title',
			'start_at:date',
			'realized_at:datetime',
			'deadline:date',
			'updated_at:datetime',
			'owner',
			'contractor',

			//'issue_id',
			//'owner_id',
			//'contractor_id',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
