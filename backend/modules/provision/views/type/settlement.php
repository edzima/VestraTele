<?php

use backend\widgets\GridView;
use common\models\issue\IssuePayCalculation;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionTypeSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\DataColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssuePayCalculation */
/* @var $searchModel ProvisionTypeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('provision', 'Provisions types for settlement: {type}', ['type' => $model->getTypeName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Types'), 'url' => ['/provision/type']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-type-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create'), ['create-settlement', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
	</p>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'name',
				'noWrap' => true,
			],
			[
				'attribute' => 'issueUserTypeName',
				'noWrap' => true,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'value',
				'value' => 'formattedValue',
				'noWrap' => true,
			],
			'is_percentage:boolean',
			'is_default:boolean',
			'calculationTypesNames',
			'issueTypesNames',
			'is_active:boolean',
			'from_at:date',
			'to_at:date',
			[
				'label' => Yii::t('provision', 'User self schema count'),
				'value' => function (ProvisionType $model): string {
					return $model->getProvisionUsers()->onlySelf()->count();
				},
			],
			[
				'class' => ActionColumn::class,
				'template' => '{create-user} {view} {update} {delete}',
				'buttons' => [
					'create-user' => function (string $url, ProvisionType $model): string {
						return Html::a('<i class="fa fa-users"></i>', ['user/create', 'typeId' => $model->id]);
					},
				],
			],
		],
	]); ?>


</div>
