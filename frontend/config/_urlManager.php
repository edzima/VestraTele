<?php

return [
	'class' => 'yii\web\UrlManager',
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'rules' => [

		// Pages
		'page/<slug>' => 'page/view',
		// Articles
		'article/page/<page>' => 'article/index',
		'articles' => 'article/index',
		'article/<slug>' => 'article/view',
		'article/category/<slug>' => 'article/category',
		'article/tag/<slug>' => 'article/tag',

		//user
		'konto/ustawienia' => 'account/default/settings',
		'konto/hierarchia' => 'account/tree/index',
		'ranking' => 'account/default/users',
		'wyloguj' => 'account/sign-in/logout',
		'logowanie' => 'account/sign-in/login',
		'przywroc-haslo' => 'account/sign-in/request-password-reset',
		'haslo-zmien' => 'account/default/password',

		//task
		'spotkania' => 'meet',

		'admin' => '/backend/web',

	],
];
