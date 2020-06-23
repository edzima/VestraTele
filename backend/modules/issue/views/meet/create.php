<?php

use backend\modules\issue\models\MeetForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model MeetForm */

$this->title = 'Dodaj spotkanie';
$this->params['breadcrumbs'][] = ['label' => 'Spotkania', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-meet-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
