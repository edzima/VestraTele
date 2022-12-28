<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\lead\models\LeadReminder;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadReminderSearch;
use common\modules\reminder\models\Reminder;
use common\widgets\grid\ActionColumn;
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
	<h1><?= Html::encode($this->title) ?></h1>
	<p>
		<?= Html::a(Yii::t('lead', 'Calendar'), ['/calendar/lead-reminder/index'], ['class' => 'btn btn-warning']) ?>
	</p>
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'leadName',
				'value' => 'lead.name',
				'label' => Yii::t('lead', 'Lead Name'),
			],

			[
				'attribute' => 'leadStatusId',
				'value' => 'lead.status.name',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Lead Status'),
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
				'attribute' => 'created_at',
				'format' => 'date',
				'label' => $searchModel->getAttributeLabel('created_at'),
				'value' => 'reminder.created_at',
			],
			[
				'attribute' => 'updated_at',
				'value' => 'reminder.updated_at',
				'label' => $searchModel->getAttributeLabel('updated_at'),
				'format' => 'date',
			],
			[
				'attribute' => 'date_at',
				'value' => 'reminder.date_at',
				'label' => $searchModel->getAttributeLabel('date_at'),
				'format' => 'date',
			],

			[
				'class' => ActionColumn::class,
				'buttons' => [
					'view' => static function (string $url, LeadReminder $model): string {
						$url = Url::toRoute(['lead/view', 'id' => $model->lead_id]);
						return Html::a(Html::icon('eye-open'), $url);
					},
				],
			],
		],
	]); ?>


</div>
