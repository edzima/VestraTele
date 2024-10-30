<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use common\models\issue\IssueType;
use common\models\settlement\search\SettlementTypeSearch;
use common\models\settlement\SettlementType;
use common\widgets\grid\ActionColumn;
use kartik\grid\ExpandRowColumn;

/** @var yii\web\View $this */
/** @var SettlementTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('settlement', 'Settlement Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['calculation/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('settlement', 'Create Settlement Type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			//   'id',
			'name',
			'is_active:boolean',
			[
				'attribute' => 'issueTypes',
				'value' => function (SettlementType $model) {
					$types = $model->issueTypes;
					if (empty($types)) {
						return Yii::t('settlement', 'All types');
					}
					$names = [];
					foreach ($types as $type) {
						$names[] = $type->name;
					}
					return Html::ul($names);
				},
				'format' => 'html',
				'filter' => IssueType::getTypesNames(),
			],
			[
				'label' => Yii::t('settlement', 'Default value'),
				'value' => function (SettlementType $model): ?float {
					return $model->getTypeOptions()->getDefaultValue();
				},
				'format' => 'currency',
			],
			[
				'label' => Yii::t('settlement', 'VAT'),
				'value' => function (SettlementType $model) {
					return $model->getTypeOptions()->vat;
				},
			],
			[
				'class' => ExpandRowColumn::class,
				'detailUrl' => 'access-details',
				'value' => function ($model, $key, $index) {
					return GridView::ROW_COLLAPSED;
				},
			],
			[
				'class' => ActionColumn::class,
				'template' => '{access} {view} {update} {delete}',
				'buttons' => [
					'access' => function ($url, SettlementType $model, $key) {
						return Html::a(
							Html::faicon('users'),
							['access', 'id' => $model->id],
							[
								'title' => Yii::t('backend', 'Access'),
								'aria-label' => Yii::t('backend', 'Access'),
							]
						);
					},
				],
			],
		],
	]); ?>


</div>
