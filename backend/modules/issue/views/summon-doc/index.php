<?php

use backend\modules\issue\models\search\SummonDocSearch;
use common\models\issue\SummonDoc;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SummonDocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Summon Docs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['/issue/summon/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-doc-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create Summon Doc'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'name',
			[
				'attribute' => 'priority',
				'value' => 'priorityName',
				'filter' => SummonDoc::getPriorityNames(),
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
