<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use common\models\issue\Summon;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\SummonDocsWidget;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Summon */
/* @var $reminderPjaxId string */

$this->title = Yii::t('common', 'Summon: {type} - {customer}', [
	'type' => $model->typeName,
	'customer' => $model->getIssueModel()->customer->getFullName(),
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->typeName;
YiiAsset::register($this);
?>
<div class="summon-view">

	<div class="form-group">

		<?= !$model->isRealized() &&
		($model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER))
			? Html::a(Yii::t('issue', 'Realize it'), ['realize', 'id' => $model->id], [
				'class' => 'btn btn-success',
				'data' => [
					'confirm' => Yii::t('issue', 'Are you sure you want to realize this summon?'),
					'method' => 'post',
				],
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_NOTE)
			? Html::a(
				Yii::t('common', 'Create note'),
				['/issue/note/create-summon', 'id' => $model->id],
				[
					'class' => 'btn btn-info',
				])
			: ''
		?>

		<?= $model->isForUser(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			? Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>



		<?= $model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR)
			? Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger pull-right',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			])
			: ''
		?>


	</div>


	<?= SummonDocsWidget::widget([
		'models' => $model->docsLink,
		'controller' => '/issue/summon-doc-link',
	]) ?>


	<div class="row">
		<div class="col-md-4">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'type.name',
					'title',
					'issue.longId:text:Sprawa',
					'owner',
					'contractor',
					[
						'attribute' => 'updater',
						'label' => Yii::t('common', 'Updater'),
						'visible' => $model->updater !== null,
					],
					'statusName',
					'entityWithCity',
					'start_at:date',
					'realize_at:datetime',
					'realized_at:datetime',
					'deadline_at:date',
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>
		</div>

		<div class="col-md-8">


			<?= $this->render('_reminder-grid', [
				'model' => $model,
				'pjaxId' => $reminderPjaxId,
			]) ?>

			<?= IssueNotesWidget::widget([
				'model' => $model->issue,
				'notes' => $model->issue->getIssueNotes()->onlySummon($model->id)->all(),
			]) ?>


		</div>
	</div>


</div>
