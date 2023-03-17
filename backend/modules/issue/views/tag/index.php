<?php

use backend\modules\issue\models\search\TagSearch;
use common\models\issue\IssueTag;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Issue Tags');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-tag-index">

	<p>
		<?= Html::a(Yii::t('issue', 'Create Issue Tag'), ['create'], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('issue', 'Types'), ['tag-type/index'], ['class' => 'btn btn-info']) ?>

	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			//	'id',
			'name',
			'description',
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => TagSearch::getTypesNames(),
			],
			'is_active:boolean',
			[
				'label' => Yii::t('issue', 'Issues'),
				'value' => function (IssueTag $tag): int {
					return count($tag->issueTagLinks);
				},
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
