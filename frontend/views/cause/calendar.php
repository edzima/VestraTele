<?php



\yii2fullcalendar\CoreAsset::register($this);
common\assets\SweetAlert::register($this);



$this->title = 'Kalendarz';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
    '@web/static/js/calendarLayer.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

?>

<div id="calendar"></div>