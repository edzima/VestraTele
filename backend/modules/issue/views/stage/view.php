<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueStage */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Etapy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-stage-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edycja', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Usuń', ['delete', 'id' => $model->id], [
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
			'id',
			'name',
			'short_name',
			'days_reminder',
		],
	]) ?>

</div>
