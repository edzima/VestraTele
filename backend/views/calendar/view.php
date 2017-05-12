<?php

use yii\helpers\Html;
use yii\helpers\Url;

\yii2fullcalendar\CoreAsset::register($this);
common\assets\SweetAlert::register($this);



  $this->title = 'Kalendarz';
  $this->params['breadcrumbs'][] = $this->title;

  $this->registerJsFile(
      '@web/static/js/calendar.js',
      ['depends' => [\yii\web\JqueryAsset::className()]]
  );

?>

  <div class="input-group">
    <span class="input-group-addon">
      <i class="fa fa-user-secret"></i> Przedstawiciel
    </span>
    <?= Html::dropDownList('agent_id', null, $agent,['prompt'=>$agent[$id],  'class' => 'form-control', 'id' => 'agent']) ?>
  </div>

  <div id="calendar"></div>

  <?= $this->render('_form');
