<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\lead\models\searches\LeadDialerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Dialers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-dialer-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>

		<?= Html::a(Yii::t('lead', 'Types'), ['dialer-type/index'], ['class' => 'btn btn-info']) ?>

	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'lead_id',
			'type_id',
			'priority',
			'created_at',
			//'updated_at',
			//'dialer_config:ntext',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
