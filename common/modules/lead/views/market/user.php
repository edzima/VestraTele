<?php

use common\helpers\Html;
use common\helpers\StringHelper;
use common\modules\lead\models\forms\LeadMarketForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\models\searches\LeadMarketSearch;
use common\modules\lead\widgets\LeadMarketAccessRequestBtnWidget;
use common\modules\lead\widgets\LeadMarketUserStatusColumn;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel LeadMarketSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Markets');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-user">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>

		<?= Html::a(Yii::t('lead', 'Access Request to self Market'), ['market-user/self-market',], [
			'class' => 'btn btn-warning',
		]) ?>

		<?= Html::a(Yii::t('lead', 'Self Access Request'), ['market-user/self',], [
			'class' => 'btn btn-success',
		]) ?>
	</p>

	<?php Pjax::begin([
		'timeout' => 3000,
	]); ?>


	<?= $this->render('_search', [
		'action' => 'user',
		'model' => $searchModel,
	]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			//'id',
			[
				'attribute' => 'addressDetails',
				'visible' => $searchModel->withAddress === null || $searchModel->withAddress,
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadMarketSearch::getStatusesNames(),
			],
			'details:ntext',
			[
				'attribute' => 'leadSource',
				'value' => 'lead.sourceName',
				'filter' => LeadMarketSearch::getLeadSourcesNames(),
				'label' => $searchModel->getAttributeLabel('leadSource'),
			],
			[
				'attribute' => 'leadType',
				'label' => Yii::t('lead', 'Type Lead'),
				'value' => 'lead.typeName',
				'filter' => LeadMarketSearch::getLeadTypesNames(),
			],
			[
				'attribute' => 'leadStatus',
				'label' => Yii::t('lead', 'Status Lead'),
				'value' => 'lead.statusName',
				'filter' => LeadMarketSearch::getLeadStatusesNames(),
			],
			[
				'attribute' => 'reportsDetails',
				'format' => 'html',
				'label' => Yii::t('lead', 'Reports'),
				'value' => static function (LeadMarket $market): string {
					$content = [];
					foreach ($market->lead->reports as $report) {
						if ($report->status->show_report_in_lead_index) {
							$details = $report->getDetails();
							if (!StringHelper::startsWith($details, LeadSmsForm::detailsPrefix())
								&& !StringHelper::startsWith($details, LeadMarketForm::detailsReportText())) {
								$content[] = Html::encode($details);
							}
						}
					}
					$content = array_filter($content, static function ($value): bool {
						return !empty(trim($value));
					});
					return implode(', ', $content);
				},
			],
			[
				'attribute' => 'creator_id',
				'value' => 'creator.fullName',
				'label' => Yii::t('lead', 'Creator'),
				'visible' => !$searchModel->selfMarket,
				'filter' => $searchModel::getCreatorsNames(),
			],
			//'updated_at',
			//'options:ntext',
			[
				'class' => LeadMarketUserStatusColumn::class,
			],
			'created_at:date',
			[
				'attribute' => 'reservedAt',
				'format' => 'date',
				'label' => Yii::t('lead', 'Reserved At'),
			],
			[
				'class' => ActionColumn::class,
				'template' => '{access-request} {view} {update} {delete}',
				'buttons' => [
					'access-request' => static function (string $url, LeadMarket $data): ?string {
						return LeadMarketAccessRequestBtnWidget::widget([
							'marketId' => $data->id,
						]);
					},
				],
				'visibleButtons' => [
					'access-request' => function (LeadMarket $data): bool {
						return $data->userCanAccessRequest(Yii::$app->user->getId());
					},
					'update' => static function (LeadMarket $data): bool {
						return $data->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
					'delete' => static function (LeadMarket $data): bool {
						return $data->status === LeadMarket::STATUS_NEW && $data->isCreatorOrOwnerLead(Yii::$app->user->getId());
					},
				],
			],
		],
	]); ?>


	<?php Pjax::end(); ?>

</div>
