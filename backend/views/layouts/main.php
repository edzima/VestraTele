<?php

use yii\bootstrap\Html;
use backend\assets\AppAsset;
use yii\web\View;

/* @var $this View */
/* @var $content string */

?>
<?php if (Yii::$app->controller->action->id === 'login'): ?>
	<?= $this->render(
		'main-login',
		['content' => $content]
	) ?>

<?php else: ?>
	<?php AppAsset::register($this); ?>
	<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/site.webmanifest">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#4894cc">
		<meta name="msapplication-TileColor" content="#4894cc">
		<meta name="theme-color" content="#4894cc">
		<?php $this->registerCsrfMetaTags() ?>
		<title><?= Html::encode(strtr('{appName} - {title}', [
					'{appName}' => Yii::$app->name,
					'{title}' => $this->title,
				])
			) ?></title>
		<?php $this->head() ?>
	</head>
	<?= Html::beginTag('body', [
		'class' => implode(' ', array_filter([
			'hold-transition',
			Yii::$app->keyStorage->get('backend.theme-skin', 'skin-blue'),
			Yii::$app->keyStorage->get('backend.layout-fixed') ? 'fixed' : null,
			Yii::$app->keyStorage->get('backend.layout-boxed') ? 'layout-boxed' : null,
			Yii::$app->keyStorage->get('backend.layout-collapsed-sidebar') ? 'sidebar-collapse' : null,
			Yii::$app->keyStorage->get('backend.layout-mini-sidebar') ? 'sidebar-mini' : null,
		])),
	]) ?>
	<?php $this->beginBody() ?>
	<div class="wrapper">
		<?= $this->render('_header.php') ?>

		<?= $this->render('_left.php') ?>

		<?= $this->render(
			'_content.php',
			['content' => $content]
		) ?>
	</div>
	<?php $this->endBody() ?>
	<?= Html::endTag('body') ?>
	</html>
	<?php $this->endPage() ?>
<?php endif; ?>
