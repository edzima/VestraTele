<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserSearch;
use common\widgets\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ProvisionUserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('provision', 'Schemas provisions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['type/index']];
$this->params['breadcrumbs'][] = Yii::t('provision', 'Schemas')
?>
<div class="provision-user-index">

	<p>
		<?= Html::a(Yii::t('provision', 'Create provision schema'), 'create', ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('provision', 'Create provision type'), '/provision/type/create', ['class' => 'btn btn-success']) ?>

	</p>

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'from_user_id',
				'format' => 'raw',
				'label' => $searchModel->onlySelf ? Yii::t('provision', 'User') : Yii::t('provision', 'From'),
				'value' => static function (ProvisionUser $data) {
					return Html::a($data->fromUser, ['user-view', 'userId' => $data->from_user_id, 'typeId' => $data->type_id]);
				},
				'filter' => $searchModel::fromUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'placeholder' => $searchModel->onlySelf ? Yii::t('provision', 'User') : Yii::t('provision', 'From'),
					],
				],
			],
			[
				'attribute' => 'to_user_id',
				'format' => 'raw',
				'visible' => !$searchModel->onlySelf,
				'label' => Yii::t('provision', 'To'),
				'value' => static function (ProvisionUser $data) {
					return Html::a($data->toUser, ['user-view', 'userId' => $data->from_user_id, 'typeId' => $data->type_id]);
				},
				'filter' => $searchModel::toUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'placeholder' => Yii::t('provision', 'To'),
					],
				],
			],
			[
				'attribute' => 'type_id',
				'value' => 'type',
				'filter' => ProvisionUserSearch::getTypesNames(),
			],
			[
				'attribute' => 'value',
				'value' => 'formattedValue',
			],
			'from_at:date',
			'to_at:date',
			[
				'attribute' => 'overwritten',
				'value' => 'isOverwritten',
				'format' => 'boolean',
				'label' => Yii::t('provision', 'Overwritten'),
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update} {delete}',
				'buttons' => [
					'view' => static function (string $url, ProvisionUser $data): string {
						return Html::a(
							Html::icon('eye-open'),
							['user-view', 'userId' => $data->to_user_id, 'typeId' => $data->type_id]);
					},
				],
			],
		],
	])
	?>


</div>
