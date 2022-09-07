<?php

use common\models\user\User;
use common\modules\issue\widgets\IssueStageChangeWidget;
use frontend\helpers\Breadcrumbs;
use frontend\helpers\Url;
use frontend\models\IssueStageChangeForm;
use yii\web\View;

/* @var $this View */
/* @var $model IssueStageChangeForm */

$this->title = Yii::t('issue', 'Change Stage: {issue}', ['issue' => $model->getIssue()->getIssueName()]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = Yii::t('issue', 'Change Stage');
?>
<div class="'issue-stage-change-view">
	<?= IssueStageChangeWidget::widget([
		'model' => $model,
		'noteDescriptionUrl' => Yii::$app->user->can(User::PERMISSION_NOTE) ? Url::to(['/note/description-list']) : null,
	]) ?>
</div>
