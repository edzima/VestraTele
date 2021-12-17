<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Url;
use backend\modules\issue\models\IssueStageChangeForm;
use common\modules\issue\widgets\IssueStageChangeWidget;
use yii\web\View;

/* @var $this View */
/* @var $model IssueStageChangeForm */

$this->title = Yii::t('issue', 'Change Stage: {issue}', [
	'issue' => $model->getIssue()->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = Yii::t('issue', 'Change Stage');

?>

<div class="issue-stage-view">

	<?= IssueStageChangeWidget::widget([
		'model' => $model,
		'noteDescriptionUrl' => Url::to(['/issue/note/description-list']),
	]) ?>

</div>

