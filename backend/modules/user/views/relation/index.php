<?php

use backend\modules\user\models\search\RelationSearch;
use backend\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel RelationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Users - Relations');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Relations');
?>
<div class="user-relation-index">

	<p>
		<?= Html::a(Yii::t('backend', 'Create User Relation'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => RelationSearch::getUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'label' => $searchModel->getAttributeLabel('user'),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('user'),
					],
				],
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => RelationSearch::getTypesNames(),
			],
			[
				'attribute' => 'to_user_id',
				'value' => 'toUser',
				'filter' => RelationSearch::getToUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'label' => $searchModel->getAttributeLabel('toUser'),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('toUser'),
					],
				],
			],

			'created_at:datetime',
			'updated_at:datetime',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
