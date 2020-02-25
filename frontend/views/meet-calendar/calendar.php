<?php

use yii\helpers\Html;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $agents string[] */

$this->title = 'Kalendarz spotkań';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meet-calendar-calendar">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= Json::encode($agents) ?>

	<?php  $this->renderFile('@web/static/calendar/index.html') ?>
</div>
