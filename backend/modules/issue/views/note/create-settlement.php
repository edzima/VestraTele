<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\IssuePayCalculation;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $settlement IssuePayCalculation */

$this->title = Yii::t('common', 'Create note for settlement: {typeName}', ['typeName' => $settlement->getTypeName()]);
$this->params['breadcrumbs'] =
	array_merge(
		Breadcrumbs::issue($settlement, false),
		Breadcrumbs::settlement($settlement)
	);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-create-settlement">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
