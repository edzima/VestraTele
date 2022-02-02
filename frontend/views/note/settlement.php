<?php

use common\models\issue\IssueSettlement;
use frontend\helpers\Breadcrumbs;
use frontend\models\IssueNoteForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $settlement IssueSettlement */

$this->title = Yii::t('issue', 'Create Issue Note for settlement: {typeName}', ['typeName' => $settlement->getTypeName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($settlement);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-note-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
