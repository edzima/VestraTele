<?php

use common\models\issue\IssueMeet;
use common\models\User;
use frontend\models\AgentMeetSearch;
use frontend\models\TeleMeetSearch;
use kartik\grid\ActionColumn;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel AgentMeetSearch | TeleMeetSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Spotkania';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-meet-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php if (Yii::$app->user->can(User::ROLE_TELEMARKETER) && Yii::$app->user->can(User::ROLE_AGENT)): ?>
		<p>

			<?= Html::a('Tele', ['tele'], ['class' => 'btn' . ($searchModel instanceof TeleMeetSearch ? ' btn-primary' : '')]) ?>
			<?= Html::a('Agent', ['agent'], ['class' => 'btn' . ($searchModel instanceof AgentMeetSearch ? ' btn-primary' : '')]) ?>

		</p>
	<?php endif; ?>

	<?= Yii::$app->user->can(User::ROLE_TELEMARKETER)
		? ('<p>'
			. Html::a('Dodaj', ['create'], ['class' => 'btn btn-success'])
			. '</p>')
		: '' ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'type_id',
				'filter' => $searchModel::getTypesNames(),
				'value' => 'type',
			],
			[
				'attribute' => 'campaign_id',
				'filter' => $searchModel::getCampaignNames(),
				'value' => 'campaign',
				'visible' => $searchModel instanceof TeleMeetSearch,
			],
			[
				'attribute' => 'status',
				'filter' => $searchModel::getStatusNames(),
				'value' => 'statusName',
			],
			'client_name',
			'client_surname',
			[
				'class' => DataColumn::class,
				'visible' => $searchModel instanceof TeleMeetSearch,
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
				'visible' => $searchModel instanceof AgentMeetSearch,
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
				'value' => function (IssueMeet $model): string {
					return $model->status <= IssueMeet::STATUS_RENEW_CONTACT
						? $model->details
						: '';
				},
				'visible' => $searchModel instanceof TeleMeetSearch,
			],
			[
				'attribute' => 'cityName',
				'value' => 'city',
				'label' => 'Miasto',
			],

			'date_at:datetime',

			'created_at:date',
			'updated_at:date',

			[
				'class' => ActionColumn::class,
				'template' => '{view}{update}',
			],
		],
	]); ?>


</div>
