<?php

use backend\helpers\Html;
use common\models\issue\IssueTagType;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssueTagType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = ['url' => ['tag/index'], 'label' => Yii::t('issue', 'Tags')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Tag Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-tag-type-view">

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
			'id',
			'name',
			'background',
			'color',
			'css-class',
			'view_issue_position',
		],
	]) ?>

</div>
