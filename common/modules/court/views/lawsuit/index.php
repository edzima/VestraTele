<?php

use common\helpers\Breadcrumbs;
use common\helpers\Html;
use common\helpers\Url;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\search\LawsuitSearch;
use common\modules\court\modules\spi\widgets\AppealsNavWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerIssuesDataColumn;
use common\widgets\grid\IssuesDataColumn;
use common\widgets\GridView;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var LawsuitSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('court', 'Lawsuits');
$this->params['breadcrumbs'][] = Breadcrumbs::issues();
$this->params['breadcrumbs'][] = $this->title;
?>

<?= AppealsNavWidget::widget([
	'activeAppeal' => $searchModel->spiAppeal,
	'getAppealFromModule' => false,
	'appealParamName' => 'appeal',
]) ?>

<div class="court-lawsuit-index">

	<p>
		<?= Html::a(
			Html::faicon('calendar'),
			['/calendar/lawsuit/index'], [
			'class' => 'btn btn-warning',
		]) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssuesDataColumn::class,
				'attribute' => 'issue_id',
			],
			[
				'class' => CustomerIssuesDataColumn::class,
				'attribute' => 'customer',
			],
			[
				'attribute' => 'court_id',
				'value' => function (Lawsuit $data): string {
					return Html::a(Html::encode($data->court->name), ['court/view', 'id' => $data->court_id]);
				},
				'format' => 'html',
				'filter' => LawsuitSearch::getCourtsNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('court_id'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			'url:url',
			[
				'attribute' => 'court_type',
				'value' => function (Lawsuit $data): string {
					return $data->court->getTypeName();
				},
				'label' => $searchModel->getAttributeLabel('court_type'),
				'filter' => LawsuitSearch::getCourtTypeNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('court_type'),
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
				'attribute' => 'is_appeal',
				'format' => 'boolean',
			],
			'due_at:datetime',
			[
				'attribute' => 'location',
				'value' => 'locationName',
				'filter' => LawsuitSearch::getLocationNames(),
			],
			[
				'attribute' => 'presence_of_the_claimant',
				'value' => 'presenceOfTheClaimantName',
				'filter' => LawsuitSearch::getPresenceOfTheClaimantNames(),
			],
			'signature_act',
			'room',
			'details',
			'created_at:date',
			'updated_at:date',
			[
				'attribute' => 'creator_id',
				'value' => 'creator',
				'label' => Yii::t('court', 'Creator'),
			],
			[
				'class' => ActionColumn::class,
				'urlCreator' => function ($action, Lawsuit $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
