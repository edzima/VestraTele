<?php

use backend\helpers\Url;
use common\models\issue\Summon;
use common\modules\issue\widgets\IssueNotesWidget;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Summon */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="summon-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
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
			'issue.longId:text:Sprawa',
			'owner',
			'contractor',
			'typeName',
			'statusName',
			'termName',
			'entityWithCity',
			'start_at:date',
			'realize_at:datetime',
			'realized_at:datetime',
			'deadline:date',

			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlySummon($model->id)->all(),
		'addUrl' => Url::to(['/issue/note/create-summon', 'id' => $model->id]),
	]) ?>


</div>
