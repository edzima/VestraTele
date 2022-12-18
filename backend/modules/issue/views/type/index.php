<?php

use backend\modules\issue\models\search\IssueTypeSearch;
use common\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssueTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Issue Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(Yii::t('backend', 'Create issue type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'name',
			'short_name',
			'vat',
			'with_additional_date:boolean',
			[
				'attribute' => 'parent_id',
				'value' => 'parentName',
				'filter' => ArrayHelper::map(IssueTypeSearch::getParents(), 'id', 'name'),
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
