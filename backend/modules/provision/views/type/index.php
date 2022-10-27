<?php

use backend\widgets\GridView;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionTypeSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\DataColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ProvisionTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('provision', 'Provisions types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-type-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
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
				'attribute' => 'issueRequiredUserTypesNames',
				'noWrap' => true,
			],
			[
				'attribute' => 'baseType.name',
				'noWrap' => true,
				'label' => Yii::t('provision', 'Base Type'),
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'value',
				'value' => 'formattedValue',
				'noWrap' => true,
			],
			'is_percentage:boolean',
			'is_default:boolean',
			'settlementTypesNames',
			'issueTypesNames',
			'is_active:boolean',
			'from_at:date',
			'to_at:date',
			[
				'label' => Yii::t('provision', 'User self schema count'),
				'value' => static function (IssueProvisionType $model): string {
					return $model->getProvisionUsers()->onlySelf()->count();
				},
			],
			[
				'class' => ActionColumn::class,
				'template' => '{create-user} {view} {update} {delete}',
				'buttons' => [
					'create-user' => static function (string $url, IssueProvisionType $type): string {
						return Html::a(
							'<i class="fa fa-user-plus"></i>',
							['user/create', 'typeId' => $type->id], [
								'title' => Yii::t('provision', 'Create provision schema'),
							]
						);
					},
				],
			],
		],
	]); ?>


</div>
