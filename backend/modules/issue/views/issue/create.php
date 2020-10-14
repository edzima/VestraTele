<?php

use backend\modules\issue\models\IssueForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueForm */

$this->title = Yii::t('backend', 'Create issue for: {customer}', ['customer' => $model->getCustomer()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getCustomer(), 'url' => ['/user/customer/view', 'id' => $model->getCustomer()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
