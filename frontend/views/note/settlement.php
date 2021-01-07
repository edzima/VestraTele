<?php

use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\IssuePayCalculation;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $settlement IssuePayCalculation */

$this->title = Yii::t('common', 'Create note for: {name}', ['name' => $settlement->getName()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->note->issue, 'url' => ['/issue/view', 'id' => $model->note->issue->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-note-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
