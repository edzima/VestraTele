<?php

use common\modules\lead\models\LeadSourceInterface;
use common\modules\lead\models\searches\LeadSourceSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadSourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Lead Sources');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Sources');
?>
<div class="lead-source-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Source'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'id' => 'lead-source-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'name',
			[
				'attribute' => 'type_id',
				'value' => 'type.name',
				'filter' => $searchModel::getTypesNames(),
				'label' => Yii::t('lead', 'Type'),
			],
			[
				'attribute' => 'url',
				'format' => 'raw',
				'value' => static function (LeadSourceInterface $leadSource): ?string {
					if (!$leadSource->getURL()) {
						return null;
					}
					return Html::a(Html::encode($leadSource->getURL()), $leadSource->getURL(), [
						'target' => '_blank',
					]);
				},
			],
			'phone:tel',
			[
				'attribute' => 'call_page_widget_id',
				'visible' => $searchModel->scenario !== $searchModel::SCENARIO_OWNER,
			],
			'sms_push_template',
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => $searchModel::getOwnersNames(),
				'label' => Yii::t('lead', 'Owner'),
				'visible' => $searchModel->scenario !== $searchModel::SCENARIO_OWNER,
			],
			'sort_index',
			'is_active:boolean',
			[
				'class' => ActionColumn::class,
				'visibleButtons' => $visibleButtons,
			],
		],
	]); ?>


</div>
