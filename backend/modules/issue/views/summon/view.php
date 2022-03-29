<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\Summon;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Summon */

$this->title = Yii::t('common', 'Summon #{id}', ['id' => $model->id]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
YiiAsset::register($this);
?>
<div class="summon-view">

	<p>
		<?= Yii::$app->user->can(User::PERMISSION_NOTE)
			? Html::a(
				Yii::t('common', 'Create note'),
				['/issue/note/create-summon', 'id' => $model->id],
				[
					'class' => 'btn btn-info',
				])
			: ''
		?>
		<?= $model->isForUser(Yii::$app->user->getId()) || Yii::$app->user->can(User::ROLE_ADMINISTRATOR)
			? Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>
		<?= $model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(User::ROLE_ADMINISTRATOR)
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

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'doc_type_id',
				'value' => $model->doc->name,
			],
			'title:text',
			'issue.longId:text:Sprawa',
			'owner',
			'contractor',
			'type.name',
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
