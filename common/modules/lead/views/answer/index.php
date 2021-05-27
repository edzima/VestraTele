<?php

use common\modules\lead\models\searches\LeadAnswerSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\lead\models\searches\LeadAnswerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Answers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reports'), 'url' => ['/lead/report/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-answer-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Answer'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'report.lead_id',
			[
				'attribute' => 'question_id',
				'value' => 'question',
				'filter' => LeadAnswerSearch::getQuestionsNames(),
			],
			'answer',
			[
				'attribute' => 'old_status_id',
				'label' => Yii::t('lead', 'Old Status'),
				'value' => 'report.oldStatus',
				'filter' => LeadAnswerSearch::getStatusNames(),
			],
			[
				'attribute' => 'status_id',
				'label' => Yii::t('lead', 'New Status'),
				'value' => 'report.status',
				'filter' => LeadAnswerSearch::getStatusNames(),
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
