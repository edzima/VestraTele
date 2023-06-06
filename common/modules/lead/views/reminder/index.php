<?php

use common\helpers\Html;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadReminderSearch;
use common\modules\lead\widgets\LeadReminderActionColumn;
use common\modules\reminder\models\Reminder;
use common\modules\reminder\widgets\ReminderGridWidget;
use common\widgets\GridView;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $searchModel LeadReminderSearch */
/* @var $dataProvider DataProviderInterface */

$this->title = Yii::t('lead', 'Lead Reminders');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="lead-reminder-index">
	<?= $searchModel->scenario === LeadReminderSearch::SCENARIO_USER
		? Html::tag('h1', Html::encode($this->title))
		: ''
	?>


	<p>
		<?= Html::a(Yii::t('lead', 'Calendar'), ['/calendar/lead-reminder/index'], ['class' => 'btn btn-warning']) ?>
	</p>
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'rowOptions' => function ($model) {
			return ReminderGridWidget::htmlRowOptions($model);
		},
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'leadName',
				'value' => 'lead.name',
				'label' => Yii::t('lead', 'Lead Name'),
			],
			[
				'attribute' => 'leadPhone',
				'value' => 'lead.phone',
				'format' => 'tel',
				'label' => Yii::t('lead', 'Phone'),
			],
			[
				'attribute' => 'leadStatusId',
				'value' => 'lead.status.name',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Lead Status'),
			],
			[
				'attribute' => 'leadDateAt',
				'value' => 'lead.date_at',
				'label' => Yii::t('lead', 'Lead Date At'),
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'attribute' => 'details',
				'value' => 'reminder.details',
				'label' => $searchModel->getAttributeLabel('details'),
				'format' => 'ntext',
			],
			[
				'attribute' => 'priority',
				'value' => 'reminder.priorityName',
				'filter' => Reminder::getPriorityNames(),
				'label' => $searchModel->getAttributeLabel('priority'),
			],
			[
				'attribute' => 'date_at',
				'value' => 'reminder.date_at',
				'label' => $searchModel->getAttributeLabel('date_at'),
				'format' => 'date',
			],
			[
				'attribute' => 'created_at',
				'format' => 'date',
				'label' => $searchModel->getAttributeLabel('created_at'),
				'value' => 'reminder.created_at',
				'visible' => $searchModel->scenario !== LeadReminderSearch::SCENARIO_USER,

			],
			[
				'attribute' => 'updated_at',
				'value' => 'reminder.updated_at',
				'label' => $searchModel->getAttributeLabel('updated_at'),
				'format' => 'date',
			],

			[
				'attribute' => 'done_at',
				'value' => 'reminder.done_at',
				'label' => $searchModel->getAttributeLabel('done_at'),
				'format' => 'date',
				'visible' => !$searchModel->hideDone,
			],
			[
				'attribute' => 'user_id',
				'value' => 'reminder.user',
				'label' => $searchModel->getAttributeLabel('user_id'),
				'filter' => $searchModel->getUsersNames(),
				'visible' => $searchModel->scenario !== LeadReminderSearch::SCENARIO_USER,
			],
			[
				'class' => LeadReminderActionColumn::class,
				'userId' => $searchModel->scenario === LeadReminderSearch::SCENARIO_USER ? Yii::$app->user->getId() : null,
			],
		],
	]);
	?>


</div>
