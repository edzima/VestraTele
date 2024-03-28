<?php

use backend\helpers\Url;
use backend\modules\settlement\models\search\IssueCostSearch;
use backend\modules\settlement\widgets\IssueCostActionColumn;
use backend\widgets\GridView;
use common\helpers\Html;
use common\models\issue\IssueCost;
use common\models\issue\IssueCostInterface;
use common\models\user\User;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueCostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Costs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-cost-index">

	<p>
		<?= IssueCost::typeExist(IssueCost::TYPE_PCC)
			? Html::a(Yii::t('backend', 'PCC Export'), ['pcc-export', Yii::$app->request->queryParams], ['class' => 'btn btn-success'])
			: ''
		?>
		<?= IssueCost::typeExist(IssueCost::TYPE_PIT_4)
			? Html::a(Yii::t('backend', 'PIT-4 Export'), ['pit-export', Yii::$app->request->queryParams], ['class' => 'btn btn-success'])
			: ''
		?>

	</p>


	<?= $this->render('_search', ['model' => $model]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $model,
		'showPageSummary' => true,

		'columns' => [
			[
				'attribute' => 'issue_id',
				'value' => function (IssueCost $model): ?string {
					if ($model->issue) {
						return Html::a($model->issue->getIssueName(), Url::issueView($model->issue_id));
					}
					return null;
				},
				'format' => 'html',
			],
			[
				'attribute' => 'issueType',
				'label' => Yii::t('common', 'Issue type'),
				'value' => function (IssueCost $model): ?string {
					if ($model->issue) {
						return Html::encode($model->issue->getTypeName());
					}
					return null;
				},
				'format' => 'html',
				'filter' => $model->getIssueTypesNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => Yii::t('common', 'Issue type'),
					],
					'pluginOptions' => [
						'dropdownAutoWidth' => true,
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
			],
			[
				'attribute' => 'issueStage',
				'label' => Yii::t('common', 'Issue stage'),
				'value' => static function (IssueCost $model): ?string {
					if ($model->issue === null) {
						return null;
					}

					return $model->issue->getStageName() . ' - ' . Yii::$app->formatter->asDate($model->issue->stage_change_at);
				},
				'filter' => IssueCostSearch::getIssueStagesNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => Yii::t('common', 'Issue stage'),
					],
					'pluginOptions' => [
						'dropdownAutoWidth' => true,
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssueCostSearch::getTypesNames(),
			],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'label' => Yii::t('backend', 'User'),
				'filter' => IssueCostSearch::getUsersNames(),
			],

			[
				'attribute' => 'value',
				'format' => 'currency',
				'pageSummary' => true,
			],
			[
				'attribute' => 'hide_on_report',
				'format' => 'boolean',
				'visible' => Yii::$app->user->can(User::PERMISSION_PROVISION),
			],
			[
				'attribute' => 'transfer_type',
				'value' => 'transferTypeName',
				'filter' => IssueCostSearch::getTransfersTypesNames(),
			],
			[
				'attribute' => 'dateRange',
				'format' => 'date',
				'value' => 'date_at',
				'label' => $model->getAttributeLabel('date_at'),
				'enableSorting' => true,
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'locale' => [
							'format' => 'YYYY-MM-DD',
						],
					],
				],
			], [
				'attribute' => 'deadlineRange',
				'format' => 'date',
				'value' => 'deadline_at',
				'label' => $model->getAttributeLabel('deadline_at'),
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'locale' => [
							'format' => 'YYYY-MM-DD',
						],
					],
				],
			],
			[
				'attribute' => 'settledRange',
				'format' => 'date',
				'value' => 'settled_at',
				'label' => $model->getAttributeLabel('settled_at'),
				'filterType' => GridView::FILTER_DATE_RANGE,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'locale' => [
							'format' => 'YYYY-MM-DD',
						],
					],
				],
			],
			[
				'value' => static function (IssueCostInterface $cost): string {
					return count($cost->settlements);
				},
				'label' => Yii::t('settlement', 'Settlements'),
				'noWrap' => true,
			],
			//	'created_at:date',
			//	'updated_at:date',
			[
				'class' => IssueCostActionColumn::class,
			],
		],
	]) ?>

</div>
