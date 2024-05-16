<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\search\IssueStageSearch;
use backend\widgets\GridView;
use common\models\issue\IssueType;
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
			'posi',
			[
				'attribute' => 'days_reminder',
				'value' => static function (IssueStage $data): ?string {
					$days = [];
					foreach ($data->stageTypes as $stageType) {
						if ($stageType->days_reminder) {
							$days[$stageType->days_reminder] = $stageType->days_reminder;
						}
					}
					if (empty($days)) {
						return null;
					}
					return implode(', ', $days);
				},
			],
			[
				'attribute' => 'calendar_background',
				'format' => 'html',
				'value' => static function (IssueStage $data): ?string {
					$colors = [];
					foreach ($data->stageTypes as $stageType) {
						if ($stageType->calendar_background) {
							$colors[] = $stageType->calendar_background;
						}
					}
					if (empty($colors)) {
						return null;
					}
					$colors = array_unique($colors);
					$spanColors = [];
					foreach ($colors as $color) {
						$spanColors[] = Html::tag('span',
							Html::encode($color), ['style' => ['color' => $color]]);
					}
					return implode(', ', $spanColors);
				},
			],
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
