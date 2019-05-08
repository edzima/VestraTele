<?php

use common\models\issue\Issue;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */

$this->title = 'Nowa sprawa';
$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
