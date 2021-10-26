<?php

use backend\modules\issue\models\IssueForm;
use common\models\message\IssueCreateMessagesForm;

/* @var $this yii\web\View */
/* @var $model IssueForm */
/* @var $messagesModel IssueCreateMessagesForm */

$this->title = Yii::t('backend', 'Create issue for: {customer}', ['customer' => $model->getCustomer()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getCustomer(), 'url' => ['/user/customer/view', 'id' => $model->getCustomer()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-create">

	<?= $this->render('_form', [
		'model' => $model,
		'messagesModel' => $messagesModel,
	]) ?>

</div>
