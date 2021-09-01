<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\Summon;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $summon Summon */

$this->title = Yii::t('issue', 'Create Issue Note for Summon: {title}', ['title' => $summon->title]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($summon);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['summon/index']];
$this->params['breadcrumbs'][] = ['label' => $summon->title, 'url' => ['summon/view', 'id' => $summon->id]];
$this->params['breadcrumbs'][] = Yii::t('issue', 'Create Issue Note');
?>
<div class="issue-note-create-summon">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
