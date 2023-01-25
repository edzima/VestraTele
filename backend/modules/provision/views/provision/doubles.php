<?php

use backend\modules\provision\models\search\ProvisionDoubleSearch;
use backend\widgets\GridView;
use common\models\provision\Provision;
use common\models\provision\ProvisionDouble;
use common\models\provision\ToUserGroupProvisionSearch;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
/* @var $searchModel ProvisionDoubleSearch */

$this->title = Yii::t('provision', 'Provision - Doubles');
$this->params['breadcrumbs'][] = [
	'label' => Yii::t('provision', 'Provisions'),
	'url' => ['index'],
];

$this->params['breadcrumbs'][] = Yii::t('provision', 'Doubles');
?>
<div class="provision-doubles">

	<p>
		<?= Html::a(Yii::t('provision', 'Reports'), [
			'report/index',
			Html::getInputName(ToUserGroupProvisionSearch::instance(), 'dateFrom') => $searchModel->dateFrom,
			Html::getInputName(ToUserGroupProvisionSearch::instance(), 'dateTo') => $searchModel->dateTo,
			Html::getInputName(ToUserGroupProvisionSearch::instance(), 'to_user_id') => $searchModel->to_user_id,
		], ['class' => 'btn btn-success'])
		?>

	</p>

	<?= $this->render('_search', [
		'model' => $searchModel,
		'action' => 'doubles',
	]) ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'settlementTypes',
				'value' => static function (Provision $model): string {
					return Html::a(
						$model->getIssueName() . ' - ' . $model->pay->calculation->getTypeName(),
						['/provision/settlement/view', 'id' => $model->pay->calculation->id]
					);
				},
				'format' => 'raw',
				'filter' => $searchModel::getSettlementTypesNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => $searchModel->getAttributeLabel('settlementTypes'),
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
			],
			[
				'attribute' => 'to_user_id',
				'value' => function (ProvisionDouble $data): string {
					return ProvisionDoubleSearch::getUsersNames()[$data->to_user_id];
				},
				'filter' => ProvisionDoubleSearch::getUsersNames(),
			],
			'fromUserString',

		],
	]); ?>


</div>
