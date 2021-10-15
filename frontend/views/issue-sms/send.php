<?php

use common\models\issue\IssueSmsForm;
use common\modules\issue\widgets\IssueSmsFormWidget;
use frontend\helpers\Breadcrumbs;
use frontend\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model IssueSmsForm */
/* @var $userTypeName string|null */

$this->title = IssueSmsFormWidget::getViewTitle($model->getIssue(), $userTypeName);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = Yii::t('issue', 'Send SMS');
?>

<div class="issue-sms-send">
	<h1><?= Html::encode($this->title) ?></h1>
	<?= IssueSmsFormWidget::widget([
		'model' => $model,
		'userTypeName' => $userTypeName,
	]) ?>
</div>


