<?php

use common\helpers\Html;
use common\models\issue\IssueNoteForm;
use common\modules\court\models\Lawsuit;
use common\modules\issue\widgets\IssueNoteFormWidget;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $lawsuit Lawsuit */

$this->title = Yii::t('court', 'Create Issue Note for Lawsuit: {signature}', ['signature' => $lawsuit->signature_act]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['lawsuit/index']];
$this->params['breadcrumbs'][] = ['label' => $lawsuit->signature_act, 'url' => ['lawsuit/view', 'id' => $lawsuit->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="court-note-create-lawsuit">

	<h3><?= Html::encode($lawsuit->signature_act) ?></h3>

	<?= IssueNoteFormWidget::widget([
		'model' => $model,
	]) ?>


</div>
