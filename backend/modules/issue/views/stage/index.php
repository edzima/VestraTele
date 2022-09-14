<?php

use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\search\IssueStageSearch;
use common\models\issue\IssueType;
use yii\grid\GridView;
use yii\helpers\Html;

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
				'attribute' => 'typesFilter',
				'value' => 'typesName',
				'label' => 'Rodzaje',
				'filter' => IssueType::getTypesNames(),
			],
			'days_reminder',
			'posi',
			[
				'label' => Yii::t('issue', 'Issues Count'),
				'value' => function (IssueStage $stage): int {
					return $stage->getIssues()->count();
				},
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
