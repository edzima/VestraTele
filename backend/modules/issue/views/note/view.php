<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\IssueNote;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueNote */

$this->title = $model->title;
$this->params['breadcrumbs'] = Breadcrumbs::issue($model);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Notes'), 'url' => ['index', 'IssueNoteSearch[issue_id]' => $model->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= !$model->isSms()
			? Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>
		<?= Yii::$app->user->canDeleteNote($model)
			? Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			]) : '' ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'issue',
			'user',
			[
				'attribute' => 'updater',
				'visible' => !empty($model->updater),
			],
			'is_pinned:boolean',
			'is_template:boolean',
			[
				'attribute' => 'typeFullName',
				'visible' => !empty($model->type),
			],
			'title',
			'description:ntext',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

</div>
