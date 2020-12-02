<?php

use backend\modules\settlement\models\IssueCostForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */

$this->title = Yii::t('backend', 'Create cost: {issue}', ['issue' => $model->getIssue()->longId]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->longId, 'url' => ['/issue/issue/view', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Costs'), 'url' => ['issue', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Create');
?>
<div class="issue-cost-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
