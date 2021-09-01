<?php

use common\models\issue\IssueNote;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueNote */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Notatki', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->issue, 'url' => ['index', 'IssueNoteSearch[issue_id]' => $model->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Delete', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'issue',
			'user',
			'is_pinned:boolean',
			'title',
			'description:ntext',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

</div>
