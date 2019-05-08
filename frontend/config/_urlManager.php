<?php

return [
	'class' => 'yii\web\UrlManager',
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'rules' => [
		// Index page
		'' => '',
		// Pages
		'page/<slug>' => 'page/view',
		// Articles
		'article/page/<page>' => 'article/index',
		'konkursy' => 'article/index',
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
		'spotkania' => 'task',
		'spotkanie' => 'task/index',
		'spotkanie/nowe' => 'task/create',
		'spotkanie/edycja' => 'task/update',

		'admin' => '/backend/web',

		//REST

		['class' => 'yii\rest\UrlRule', 'controller' => 'apicalendar'],

	],
];
