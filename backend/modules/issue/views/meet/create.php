<?php

use backend\modules\issue\models\MeetForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model MeetForm */

$this->title = Yii::t('backend', 'Create meet');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Meets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-meet-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
