<?php

use common\helpers\Breadcrumbs;
use common\models\issue\IssueInterface;
use common\modules\court\models\LawsuitIssueForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var LawsuitIssueForm $model */
/** @var IssueInterface $issue */

$this->title = Yii::t('court', 'Create Lawsuit for Issue: {issue}', [
	'issue' => $issue->getIssueName(),
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lawsuit-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
