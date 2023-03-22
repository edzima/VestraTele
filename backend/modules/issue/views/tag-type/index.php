<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\TagTypeSearch;
use backend\widgets\GridView;
use common\models\issue\IssueTagType;
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
			'name',
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
			[
				'attribute' => 'background',
				'contentOptions' => function (IssueTagType $data): array {
					$options = [];
					if (!empty($data->background)) {
						$options['style']['background-color'] = $data->background;
					}
					return $options;
				},
			],
			[
				'attribute' => 'color',
				'contentOptions' => function (IssueTagType $data): array {
					$options = [];
					if (!empty($data->color)) {
						$options['style']['background-color'] = $data->color;
					}
					return $options;
				},
			],
			'css_class',

			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>
