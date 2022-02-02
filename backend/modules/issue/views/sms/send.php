<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueSmsForm;
use common\modules\issue\widgets\IssueSmsFormWidget;
use yii\web\View;

/* @var $this View */
/* @var $model IssueSmsForm */
/* @var $userTypeName string|null */

$this->title = IssueSmsFormWidget::getViewTitle($model->getIssue(), $userTypeName);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = Yii::t('issue', 'Send SMS');
?>

<div class="issue-sms-send">
	<?= IssueSmsFormWidget::widget([
		'model' => $model,
		'userTypeName' => $userTypeName,
	]) ?>
</div>


