<?php

use common\modules\lead\models\searches\LeadReportSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Reports');
?>
<div class="lead-report-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'lead.type',
			'owner',
			'status',
			'oldStatus',
			'schema',
			'formattedDetails',
			'created_at',
			'updated_at',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
