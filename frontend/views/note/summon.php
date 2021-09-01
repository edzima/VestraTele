<?php

use common\models\issue\Summon;
use frontend\helpers\Breadcrumbs;
use frontend\models\IssueNoteForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $summon Summon */

$this->title = Yii::t('issue', 'Create Issue Note for Summon: {title}', ['title' => $summon->title]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($summon);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), ['url' => ['summon/index']]];
$this->params['breadcrumbs'][] = ['label' => $summon->title, ['url' => ['summon/view', 'id' => $summon->id]]];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-note-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
