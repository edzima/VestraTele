<?php

use backend\widgets\CsvForm;
use common\models\issue\IssueMeet;
use frontend\models\AgentMeetSearch;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel AgentMeetSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Lead';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-meet-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
		<?= CsvForm::widget() ?>
	</p>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'campaign_id',
				'filter' => AgentMeetSearch::getCampaignNames(),
				'value' => 'campaignName',
			],
			[
				'attribute' => 'type_id',
				'filter' => AgentMeetSearch::getTypesNames(),
				'value' => 'type',
			],
			'created_at:date',
			'client_name',
			'client_surname',
			'phone',
			[
				'attribute' => 'cityName',
				'value' => 'customerAddress.city.name',
				'label' => 'Miasto',
			],
			[
				'attribute' => 'regionId',
				'value' => 'customerAddress.city.region.name',
				'label' => 'Województwo',
				'filter' => AgentMeetSearch::getRegionsNames(),
			],
			[
				'attribute' => 'details',
				'format' => 'ntext',
			],
			'date_at:date',
			'updated_at:date',
			[
				'attribute' => 'status',
				'filter' => AgentMeetSearch::getStatusNames($searchModel->withArchive),
				'value' => 'statusName',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view}{update}{delete}',
				'visibleButtons' => [
					'delete' => static function (IssueMeet $model): bool {
						return !$model->hasCampaign();
					},
					'view' => static function (IssueMeet $model) use ($searchModel): bool {
						return !$model->isArchived() || $searchModel->withArchive;
					},
				],
			],
		],
	]); ?>

</div>
