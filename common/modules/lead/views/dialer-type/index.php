<?php

use common\modules\lead\models\LeadDialerType;
use common\modules\lead\models\searches\LeadDialerTypeSearch;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadDialerTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Dialer Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Dialers'), 'url' => ['dialer/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Types');
?>
<div class="lead-dialer-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Dialer Type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'name',
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => LeadDialerTypeSearch::getStatusesNames(),
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => LeadDialerTypeSearch::getTypesNames(),
			],
			'did',
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => LeadDialerTypeSearch::getUsersNames(),
			],
			[
				'label' => Yii::t('lead', 'Count'),
				'value' => static function (LeadDialerType $model): int {
					return $model->getLeadDialers()->count();
				},
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
