<?php

use common\modules\lead\models\searches\LeadCampaignSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Lead Campaigns');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Campaigns');
?>
<div class="lead-campaign-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Campaign'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'id' => 'lead-campaign-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'id',
			'name',
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'visible' => $searchModel->scenario !== $searchModel::SCENARIO_OWNER,
			],
			'sort_index',
			[
				'class' => ActionColumn::class,
				'visibleButtons' => $visibleButtons,
			],
		],
	]); ?>


</div>
