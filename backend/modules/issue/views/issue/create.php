<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueForm;
use common\models\message\IssueCreateMessagesForm;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $leadsDataProvider DataProviderInterface */
/* @var $model IssueForm */
/* @var $messagesModel IssueCreateMessagesForm */

$this->title = Yii::t('backend', 'Create issue for: {customer}', ['customer' => $model->getCustomer()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['/user/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getCustomer(), 'url' => ['/user/customer/view', 'id' => $model->getCustomer()->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-create">

	<?= $this->render('_leads', [
		'dataProvider' => $leadsDataProvider,
		'agentInputId' => Html::getInputId($model, 'agent_id'),
		'teleInputId' => Html::getInputId($model, 'tele_id'),
		'leadInputId' => Html::getInputId($model, 'lead_id'),
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
		'messagesModel' => $messagesModel,
	]) ?>

</div>
