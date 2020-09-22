<?php

use backend\widgets\GridView;
use common\models\issue\Issue;
use common\models\issue\Summon;
use kartik\grid\ActionColumn;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $baseUrl string */
/* @var $addBtn bool */
/* @var $editBtn bool */
/* @var $dataProvider ActiveDataProvider */
/* @var $actionColumnTemplate string */

?>

<fieldset>
	<legend><?= Yii::t('common', 'Summons') ?>
		<?php if ($addBtn): ?>
			<?= Html::a(
				'<i class="fa fa-plus"></i>',
				[$baseUrl . 'create', 'issueId' => $model->id], [
				'class' => 'btn btn-xs btn-success',
			]) ?>
		<?php endif; ?>

		<button class="btn toggle pull-right" data-toggle="#summons-details">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>
	<div id="summons-details">
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'columns' => [
				['class' => 'yii\grid\SerialColumn'],
				'typeName',
				'statusName',
				'termName',
				'title',
				'start_at:date',
				'realized_at:datetime',
				'deadline:date',
				'contractor',
				[
					'class' => ActionColumn::class,
					'urlCreator' => function (string $action, Summon $model) use ($baseUrl) {
						return Url::to([$baseUrl . $action, 'id' => $model->id]);
					},
					'template' => $actionColumnTemplate,
				],
			],
		]) ?>
	</div>
</fieldset>
