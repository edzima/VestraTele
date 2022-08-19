<?php

use common\helpers\Html;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\searches\LeadMarketSearch;
use common\modules\lead\Module;
use common\modules\lead\widgets\LeadMarketUserStatusColumn;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel LeadMarketSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Markets');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-market-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?php if (!Module::getInstance()->onlyUser): ?>
			<?= Html::a(Yii::t('lead', 'Market Users'), ['market-user/index',], [
				'class' => 'btn btn-success',
			]) ?>
		<?php else: ?>
			<?= Html::a(Yii::t('lead', 'Access Request to self Market'), ['market-user/self-market',], [
				'class' => 'btn btn-warning',
			]) ?>

			<?= Html::a(Yii::t('lead', 'Self Access Request'), ['market-user/self',], [
				'class' => 'btn btn-success',
			]) ?>
		<?php endif; ?>

	</p>

	<?= $this->render('_search', [
		'action' => 'index',
		'model' => $searchModel,
	]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadMarketSearch::getStatusesNames(),
			],
			[
				'attribute' => 'leadName',
				'value' => function (LeadMarket $data): string {
					return Html::a(Html::encode($data->lead->getName()), [
						'lead/view', 'id' => $data->lead_id,
					]);
				},
				'format' => 'html',
			],
			[
				'attribute' => 'leadStatus',
				'value' => 'lead.statusName',
				'filter' => LeadMarketSearch::getLeadStatusesNames(),
			],
			'details:ntext',

			[
				'attribute' => 'visibleArea',
				'value' => 'marketOptions.visibleAreaName',
				'label' => Yii::t('lead', 'Visible Area'),
				'filter' => LeadMarketSearch::getVisibleAreaNames(),
			],
			[
				'attribute' => 'creator_id',
				'value' => 'creator.fullName',
				'label' => Yii::t('lead', 'Creator'),
				'filter' => LeadMarketSearch::getCreatorsNames(),
			],
			[
				'class' => LeadMarketUserStatusColumn::class,
			],
			'created_at:datetime',
			'updated_at:datetime',
			[
				'attribute' => 'reservedAt',
				'format' => 'date',
				'label' => Yii::t('lead', 'Reserved At'),
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update} {delete}',
			],
		],
	]); ?>


</div>
