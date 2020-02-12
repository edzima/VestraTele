<?php

use common\models\issue\IssueMeet;
use common\models\User;
use common\models\Wojewodztwa;
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
$userId = Yii::$app->user->getId();
?>
<div class="issue-meet-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php if (Yii::$app->user->can(User::ROLE_TELEMARKETER) && Yii::$app->user->can(User::ROLE_AGENT)): ?>
		<p>

			<?= Html::a('Tele', ['tele'], ['class' => 'btn' . ($searchModel instanceof TeleMeetSearch ? ' btn-primary' : '')]) ?>
			<?= Html::a('Agent', ['agent'], ['class' => 'btn' . ($searchModel instanceof AgentMeetSearch ? ' btn-primary' : '')]) ?>
			<?= Yii::$app->user->can(User::ROLE_MEET) ?
				Html::a('Wszystkie', ['all'], ['class' => 'btn' . (Yii::$app->controller->action->id === 'all' ? ' btn-primary' : '')])
				: '' ?>


		</p>
	<?php endif; ?>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

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
				'attribute' => 'campaign_id',
				'filter' => $searchModel::getCampaignNames(),
				'value' => 'campaign',
				'visible' => $searchModel instanceof TeleMeetSearch || Yii::$app->user->can(User::ROLE_MEET),
			],
			[
				'attribute' => 'type_id',
				'filter' => $searchModel::getTypesNames(),
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
		//		'visible' => $searchModel instanceof TeleMeetSearch,
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
				'value' => static function (IssueMeet $model): string {
					return $model->status <= IssueMeet::STATUS_RENEW_CONTACT
						? $model->details
						: '';
				},
				'visible' => $searchModel instanceof TeleMeetSearch,
			],
			'date_at:date',
			'updated_at:date',
			[
				'attribute' => 'status',
				'filter' => $searchModel::getStatusNames(),
				'value' => 'statusName',
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view}{update}',
				'visibleButtons' => [
					'update' => static function (IssueMeet $model) use ($userId) {
						return $model->isForUser($userId);
					},
				],
			],
		],
	]); ?>


</div>
