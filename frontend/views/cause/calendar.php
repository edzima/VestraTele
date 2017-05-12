<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;




\yii2fullcalendar\CoreAsset::register($this);
common\assets\SweetAlert::register($this);



$this->title = 'Kalendarz';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
    '@web/static/js/calendarLayer.js',
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


echo Html::a("Link", ["cause/view", 'id'=>4 ]);
?>

<div id="calendar"></div>
<?= Html::button('Update Company', ['value' => Url::to(['update-ajax', 'id'=>4]), 'title' => 'Updating Company', 'class' => 'showModalButton btn btn-success']); ?>