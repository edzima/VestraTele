<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueEntityResponsible */

$this->title = 'Dodaj podmiot odpowiedzialny';
$this->params['breadcrumbs'][] = ['label' => 'Podmioty odpowiedzialne', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-entity-responsible-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
