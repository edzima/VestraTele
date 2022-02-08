<?php

use common\helpers\Html;
use common\modules\lead\models\DuplicateLead;
use common\modules\lead\models\searches\DuplicateLeadSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use kartik\select2\Select2;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $searchModel DuplicateLeadSearch */
/* @var $dataProvider DataProviderInterface */

$this->title = Yii::t('lead', 'Duplicates Leads');

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="duplicate-lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_search', [
		'model' => $searchModel,
	])
	?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'id' => 'leads-grid',
		'columns' => [
			[
				'attribute' => 'name',
				'contentBold' => true,
			],
			[
				'attribute' => 'phone',
				'format' => 'tel',
				'noWrap' => true,
				'width' => '124px',
			],
			[
				'attribute' => 'type_id',
				'value' => function (DuplicateLead $lead): string {
					return implode(', ', $lead->getSameContactsTypesNames());
				},
				'contentBold' => true,
				'filter' => $searchModel::getTypesNames(),
				'label' => Yii::t('lead', 'Type'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Type'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			[
				'attribute' => 'status_id',
				'value' => function (DuplicateLead $lead): string {
					return implode(', ', $lead->getSameContactsStatusesNames());
				},
				'filter' => $searchModel::getStatusNames(),
				'label' => Yii::t('lead', 'Status'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Status'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			[
				'attribute' => 'duplicateCount',
				'label' => Yii::t('lead', 'Duplicate Count'),
				'value' => function (DuplicateLead $lead): string {
					return count($lead->getSameContacts(false));
				},
			],
			[
				'class' => ActionColumn::class,
				'controller' => '/lead/lead',
			],
		],
	]);
	?>

</div>
