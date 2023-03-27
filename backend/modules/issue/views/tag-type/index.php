<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\TagTypeSearch;
use backend\widgets\GridView;
use common\models\issue\IssueTagType;
use common\modules\issue\widgets\IssueTagsWidget;
use common\widgets\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel TagTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Issue Tag Types');

$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = ['url' => ['tag/index'], 'label' => Yii::t('issue', 'Tags')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-tag-type-index">


	<p>
		<?= Html::a(Yii::t('backend', 'Create Issue Tag Type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'name',
				'contentOptions' => function (IssueTagType $data): array {
					return IssueTagsWidget::tagTypeOptions($data);
				},
			],
			[
				'attribute' => 'view_issue_position',
				'value' => 'viewIssuePositionName',
				'filter' => IssueTagType::getViewIssuePositionNames(),
			],
			[
				'attribute' => 'issues_grid_position',
				'value' => 'issuesGridPositionName',
				'filter' => IssueTagType::getIssuesGridPositionNames(),
			],
			'css_class',
			'sort_order',
			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>
