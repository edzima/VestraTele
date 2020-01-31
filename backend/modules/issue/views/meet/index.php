<?php

use common\models\issue\IssueMeet;
use common\models\User;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\issue\IssueMeetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Spotkania';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-meet-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
				'value' => 'type',
			],
			[
				'attribute' => 'status',
				'filter' => IssueMeet::getStatusNames(),
				'value' => 'statusName',
			],
			'client_name',
			'client_surname',
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
				'attribute' => 'details',
				'format' => 'ntext',
				'value' => static function (IssueMeet $model): string {
					return $model->status <= IssueMeet::STATUS_RENEW_CONTACT
						? $model->details
						: '';
				},
			],
			[
				'attribute' => 'cityName',
				'value' => 'city',
				'label' => 'Miasto',
			],

			'date_at:datetime',

			'created_at:date',
			'updated_at:date',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
