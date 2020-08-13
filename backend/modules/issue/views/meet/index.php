<?php

use backend\widgets\CsvForm;
use common\models\issue\IssueMeet;
use common\models\issue\IssueMeetSearch;
use common\models\User;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssueMeetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Spotkania';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-meet-index relative">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>
	<?= CsvForm::widget() ?>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'campaign_id',
				'filter' => IssueMeetSearch::getCampaignNames(),
				'value' => 'campaignName',
			],
			[
				'attribute' => 'type_id',
				'filter' => IssueMeetSearch::getTypesNames(true),
				'value' => 'type.short_name',
			],
			'created_at:date',
			'client_name',
			'client_surname',
			'phone',
			[
				'attribute' => 'cityName',
				'value' => 'city',
				'label' => 'Miasto',
			],
			[
				'attribute' => 'stateId',
				'value' => 'state.name',
				'label' => 'Województwo',
				'filter' => IssueMeetSearch::getStateNames(),
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'value' => 'agent',
				'filter' => User::getSelectList([User::ROLE_AGENT, User::ROLE_MEET]),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => 'Agent',
					],
				],
				'contentOptions' => [
					'class' => 'ellipsis',
				],
			],

			[
				'attribute' => 'details',
				'format' => 'ntext',
			],
			'date_at:date',
			'updated_at:date',
			[
				'attribute' => 'status',
				'filter' => IssueMeetSearch::getStatusNames($searchModel->withArchive),
				'value' => 'statusName',
			],

			[
				'class' => ActionColumn::class,
				'template' => '{view}{update}{delete}',
				'visibleButtons' => [
					'view' => static function (IssueMeet $model) use ($searchModel): bool {
						return !$model->isArchived() || $searchModel->withArchive;
					},
				],
			],
		],
	]); ?>


</div>
