<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

\yii2fullcalendar\CoreAsset::register($this);
common\assets\SweetAlert::register($this);



  $this->title = 'Calendar';
  $this->params['breadcrumbs'][] = $this->title;

  $this->registerJsFile(
      '@web/static/js/calendarAgent.js',
      ['depends' => [\yii\web\JqueryAsset::className()]]
  );
/*

  $this->registerJs("
		var id = $id;
        var url = 'agenttask?id='+id;
        $('#calendar').fullCalendar( 'addEventSource',     {
              url: url,
          } );
         ");
*/

?>

<div class="common-button">
    <p>
        <?= Html::a('Send Message', ['message/create'] ,['class' => 'btn btn-danger','id' => 'buttonMessage','data-pjax' => '0']) ?>
    </p>
</div>


<div id="calendar"> </div>
<?= $this->render('_newsForm');
