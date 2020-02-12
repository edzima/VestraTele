<?php

use backend\widgets\CsvForm;
use common\models\issue\IssueMeet;
use common\models\issue\IssueMeetSearch;
use common\models\User;
use common\models\Wojewodztwa;
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


	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'campaign_id',
				'filter' => IssueMeet::getCampaignNames(),
				'value' => 'campaign',
			],
			[
				'attribute' => 'type_id',
				'filter' => IssueMeet::getTypesNames(),
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
				'filter' => Wojewodztwa::getSelectList(),
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'tele_id',
				'value' => 'tele',
				'filter' => User::getSelectList([User::ROLE_TELEMARKETER]),
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
					],
					'options' => [
						'placeholder' => 'Tele',
					],
				],
				'contentOptions' => [
					'class' => 'ellipsis',
				],
			],
			[
				'class' => DataColumn::class,
				'filterType' => GridView::FILTER_SELECT2,
				'attribute' => 'agent_id',
				'value' => 'agent',
				'filter' => User::getSelectList([User::ROLE_AGENT]),
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
				'value' => static function (IssueMeet $model): string {
					return $model->status <= IssueMeet::STATUS_RENEW_CONTACT
						? $model->details
						: '';
				},
			],
			'date_at:date',
			'updated_at:date',
			[
				'attribute' => 'status',
				'filter' => IssueMeet::getStatusNames(),
				'value' => 'statusName',
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
