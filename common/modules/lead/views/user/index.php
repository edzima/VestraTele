<?php

use common\modules\lead\models\searches\LeadUsersSearch;
use common\modules\lead\Module;
use yii\helpers\Html;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Users');
?>
<div class="lead-user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead User'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'lead_id',
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => Module::userNames(),
				'filterType' => GridView::FILTER_SELECT2,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => LeadUsersSearch::getTypesNames(),
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
