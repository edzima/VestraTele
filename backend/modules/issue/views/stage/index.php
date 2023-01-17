<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\search\IssueStageSearch;
use backend\widgets\GridView;
use common\models\issue\IssueType;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\DataColumn;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel IssueStageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Stages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-stage-index">

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(Yii::t('backend', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('issue', 'Types'), ['type/index'], ['class' => 'btn btn-info']) ?>

	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'name',
			'short_name',
			[
				'class' => DataColumn::class,
				'attribute' => 'typesFilter',
				'value' => 'typesShortNames',
				'label' => Yii::t('issue', 'Issues Types'),
				'filter' => IssueType::getTypesNamesWithShort(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => Yii::t('issue', 'Issues Types'),
					],
					'pluginOptions' => [
						'dropdownAutoWidth' => true,
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
				'contentBold' => true,
			],
			[
				'attribute' => 'days_reminder',
				'value' => function (IssueStage $model): ?string {
					$days = [];
					foreach ($model->stageTypes as $stageType) {
						if ($stageType->days_reminder) {
							$days[] = $stageType->days_reminder;
						}
					}
					if (empty($days)) {
						return null;
					}
					$days = array_unique($days);
					return implode(', ', $days);
				},
			],
			'posi',
			[
				'label' => Yii::t('issue', 'Issues Count'),
				'value' => function (IssueStage $stage): int {
					return $stage->getIssues()->count();
				},
			],
			['class' => ActionColumn::class],
		],
	]); ?>
</div>
