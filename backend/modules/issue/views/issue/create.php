<?php

use backend\modules\issue\models\IssueForm;
use backend\modules\user\widgets\DuplicateUserGridView;
use common\models\message\IssueCreateMessagesForm;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $duplicatesCustomersDataProvider ActiveDataProvider */
/* @var $model IssueForm */
/* @var $messagesModel IssueCreateMessagesForm */

$this->title = Yii::t('backend', 'Create issue for: {customer}', ['customer' => $model->getCustomer()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getCustomer(), 'url' => ['/user/customer/view', 'id' => $model->getCustomer()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-create">

	<?= DuplicateUserGridView::widget([
		'dataProvider' => $duplicatesCustomersDataProvider,
		'showOnEmpty' => false,
		'emptyText' => false,
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
		'messagesModel' => $messagesModel,
	]) ?>

</div>
