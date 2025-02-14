<?php

use common\helpers\Breadcrumbs;
use common\helpers\Html;
use common\helpers\Url;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\search\LawsuitSearch;
use common\modules\court\Module;
use common\modules\court\modules\spi\widgets\AppealsNavWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerIssuesDataColumn;
use common\widgets\grid\DateTimeColumn;
use common\widgets\grid\IssuesDataColumn;
use common\widgets\GridView;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;

/** @var yii\web\View $this */
/** @var LawsuitSearch $searchModel */
/** @var ActiveDataProvider $dataProvider */
/** @var bool $withSPI */

$this->title = Yii::t('court', 'Lawsuits');
$this->params['breadcrumbs'][] = Breadcrumbs::issues();
if ($searchModel->spiAppeal) {
	$this->params['breadcrumbs'][] = [
		'label' => Yii::t('court', 'Lawsuits'),
		'url' => ['index'],
	];
	$name = Module::getInstance()->getSPI()::getAppealsNames()[$searchModel->spiAppeal] ?? null;
	if ($name) {
		$this->params['breadcrumbs'][] = $name;
	}
} else {
	$this->params['breadcrumbs'][] = $this->title;
}
?>

<?= AppealsNavWidget::widget([
	'activeAppeal' => $searchModel->spiAppeal,
	'getAppealFromModule' => false,
	'activeFromModule' => false,
	'appealParamName' => 'appeal',
	'module' => Module::getInstance()->getSPI(),
]) ?>

<div class="court-lawsuit-index">

	<p>
		<?= Html::a(
			Html::faicon('calendar'),
			['/calendar/lawsuit/index'], [
			'class' => 'btn btn-warning',
		]) ?>

		<?= Yii::$app->user->can(
			Module::PERMISSION_SPI_LAWSUIT_DETAIL
		) ? Html::a(
			Html::faicon('bell'),
			['/court/spi/notification/index'],
			[
				'class' => 'btn btn-info',
				'title' => Yii::t('court', 'Notification'),
			]
		) : '' ?>
	</p>

	<?= $this->render('_search', [
		'model' => $searchModel,
		'withSPI' => $withSPI,
	]); ?>

	<?= $this->render('_chart', [
		'model' => $searchModel,
		'dataProvider' => $dataProvider,
	]) ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'rowOptions' => function (Lawsuit $model): array {
			$options = [];
			if ($model->is_appeal) {
				Html::addCssClass($options, 'warning');
			}
			return $options;
		},
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
			[
				'attribute' => 'result',
				'filter' => $searchModel->getResultNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('result'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			'signature_act',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'created_at',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'updated_at',
			],
			[
				'attribute' => 'creator_id',
				'value' => 'creator',
				'label' => Yii::t('court', 'Creator'),
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'spi_last_update_at',
				'visible' => $withSPI,
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
