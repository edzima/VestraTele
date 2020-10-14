<?php

use backend\modules\issue\models\IssueForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueForm */

$this->title = Yii::t('backend', 'Update {issueID} for: {customer}', [
	'issueID' => $model->getModel(),
	'customer' => $model->getCustomer(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getCustomer(), 'url' => ['/user/customer/view', 'id' => $model->getCustomer()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="issue-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
