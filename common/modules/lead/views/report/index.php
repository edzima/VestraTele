<?php

use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
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

	<p>
		<?= Html::a(Yii::t('lead', 'Lead Questions'), ['question/index'], [
			'class' => 'btn btn-info',
		])
		?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'lead_type_id',
				'value' => 'lead.source.type',
				'filter' => LeadType::getNames(),
			],
			'owner',
			[
				'attribute' => 'status_id',
				'value' => 'status',
				'filter' => LeadStatus::getNames(),
			],
			[
				'attribute' => 'old_status_id',
				'value' => 'oldStatus',
				'filter' => LeadStatus::getNames(),
			],
			'answersQuestions',
			'details:text',
			'created_at:date',
			'updated_at:date',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
