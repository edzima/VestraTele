<?php

use common\helpers\Html;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadQuestionSearch;
use common\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel LeadQuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Questions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reports'), 'url' => ['/lead/report/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-question-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Question'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'name',
			'placeholder',
			[
				'attribute' => 'status_id',
				'value' => 'status.name',
				'filter' => LeadStatus::getNames(),
			],
			[
				'attribute' => 'type_id',
				'value' => 'type.name',
				'filter' => LeadType::getNames(),

			],
			'is_active:boolean',
			'is_boolean:boolean',
			'is_required:boolean',
			'show_in_grid:boolean',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
