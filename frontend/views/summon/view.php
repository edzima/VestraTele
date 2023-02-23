<?php

use common\models\issue\Summon;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\SummonDocsWidget;
use frontend\controllers\SummonController;
use frontend\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Summon */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue->longId, 'url' => ['/issue/view', 'id' => $model->issue->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="summon-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>


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

		<?= SummonController::canUpdate($model)
			? Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= Yii::$app->user->can(User::PERMISSION_NOTE)
			? Html::a(Yii::t('common', 'Create note'), ['/note/summon', 'id' => $model->id], [
				'class' => 'btn btn-info pull-right',
			])
			: ''
		?>


	</p>

	<?= SummonDocsWidget::widget([
		'models' => $model->docsLink,
		'controller' => '/summon-doc',
	]) ?>


	<div class="row">

		<div class="col-md-5">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'issue.longId:text:Sprawa',
					'owner',
					'contractor',
					[
						'attribute' => 'updater',
						'visible' => $model->updater !== null,
					],
					'typeName',
					'statusName',
					'entityWithCity',
					'start_at:date',
					'realize_at:datetime',
					'realized_at:datetime',
					[
						'attribute' => 'deadline_at',
						'format' => 'date',
						'options' => [
							'class' => 'red-text',
						],
					],

					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>
		</div>
		<div class="col-md-7">

			<?= IssueNotesWidget::widget([
				'model' => $model->issue,
				'notes' => $model->issue->getIssueNotes()->joinWith('user.userProfile')->onlySummon($model->id)->all(),
			]) ?>
		</div>
	</div>


</div>
