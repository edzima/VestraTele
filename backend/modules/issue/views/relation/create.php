<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\IssueInterface;
use common\models\issue\IssueRelation;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueRelation */
/* @var $issue IssueInterface */

$this->title = Yii::t('issue', 'Create Issue Relation: {issue}', [
	'issue' => $issue->getIssueName(),
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-relation-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
