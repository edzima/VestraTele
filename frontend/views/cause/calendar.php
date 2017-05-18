<?php



\yii2fullcalendar\CoreAsset::register($this);
common\assets\Toggle::register($this);


use lo\widgets\Toggle;


$this->title = 'Kalendarz';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
    '@web/static/js/calendarLayer.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);


?>

<label>Powiadomienia

<?=Toggle::widget(
[
'name' => 'status', // input name. Either 'name', or 'model' and 'attribute' properties must be specified.
'checked' => true,
'id'=> 'toggle',
'options' => [
        'data-on' => 'Tak',
        'data-off' => 'Nie'
], // checkbox options. More data html options [see here](http://www.bootstraptoggle.com)
]
)?>
</label>

<div id="calendar"></div>
