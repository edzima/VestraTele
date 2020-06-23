<?php

use frontend\models\meet\MeetForm;

/* @var $this yii\web\View */
/* @var $model MeetForm */

$this->title = 'Dodaj Lead';
$this->params['breadcrumbs'][] = ['label' => 'Lead', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-meet-create">

	<h1>Dodaj lead</h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
