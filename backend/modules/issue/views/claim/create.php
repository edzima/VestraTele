<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\IssueClaim;

/* @var $this yii\web\View */
/* @var $model IssueClaim */

$this->title = Yii::t('issue', 'Create Claim: {issue}', [
	'issue' => $model->issue->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Claims'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-claim-create">

	<?= $this->render('_form', [
		'model' => $model,
		'onlyField' => false,
	]) ?>

</div>
