<?php

use common\modules\lead\models\searches\LeadSourceSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadSourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Sources');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Sources');
?>
<div class="lead-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Source'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'name',
			'url',
			'owner',
			'sort_index',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
