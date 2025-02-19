<?php

return [
	'class' => 'yii\web\UrlManager',
	'hostInfo' => Yii::getAlias('@backendUrl'),
	'baseUrl' => '',
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'rules' => [
		'issue/summon/create/<typeId>' => 'issue/summon/create',
		'issue/summon/create-multiple/<typeId>' => 'issue/summon/create-multiple',
	],
];
