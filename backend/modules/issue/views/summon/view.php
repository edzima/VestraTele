<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\Summon;
use common\models\user\Worker;
use common\modules\issue\widgets\IssueNotesWidget;
use common\modules\issue\widgets\SummonDocsWidget;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Summon */

$this->title = Yii::t('common', 'Summon: {type}', ['type' => $model->typeName]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->typeName;
YiiAsset::register($this);
?>
<div class="summon-view">

	<p>
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
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			])
			: ''
		?>
	</p>


	<?= SummonDocsWidget::widget([
		'models' => $model->docsLink,
		'controller' => '/issue/summon-doc-link',
	]) ?>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'type.name',
			'title',
			'issue.longId:text:Sprawa',
			'owner',
			'contractor',
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

	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlySummon($model->id)->all(),
	]) ?>


</div>
