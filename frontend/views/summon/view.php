<?php

use common\modules\issue\widgets\IssueNotesWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\issue\Summon */

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
		<?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
			[
				'attribute' => 'deadline',
				'format' => 'date',
				'options' => [
						'class' => 'red-text'
				]
			],

			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlySummon($model->id)->all(),
		'addUrl' => Url::to(['/note/create-summon', 'id' => $model->id]),
		'noteOptions' => [
			'removeBtn' => false,
		],
	]) ?>


</div>
