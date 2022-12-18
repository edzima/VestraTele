<?php

use common\models\issue\IssueType;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'parentName',
				'value' => function () use ($model): string {
					$name = $model->getParentName();
					if ($name) {
						return Html::a(Html::encode($name), [
							'view', 'id' => $model->parent_id,
						]);
					}
					return '';
				},
				'visible' => $model->parent !== null,
				'format' => 'html',
			],
			'name',
			'short_name',
			'vat',
			'with_additional_date:boolean',
		],
	]) ?>

</div>
