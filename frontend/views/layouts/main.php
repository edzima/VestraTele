<?php

use common\models\user\User;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\models\NavItem;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php $this->registerCsrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
	<?php NavBar::begin([
		'brandLabel' => Yii::$app->name,
		'brandUrl' => Yii::$app->homeUrl,
		'options' => [
			'class' => 'navbar-inverse navbar-fixed-top',
		],
	]);

	if (Yii::$app->user->isGuest) {
		$menuItems[] = ['label' => Yii::t('frontend', 'Login'), 'url' => ['/site/login']];
	} else {
		$menuItems[] = [
			'label' => 'Lead',
			'url' => '#',
			'visible' => Yii::$app->user->can(User::PERMISSION_MEET),

			'items' => [
				[
					'label' => Yii::t('common', 'Browse'),
					'url' => ['/meet/index'],
				],
				[
					'label' => 'Nowy',
					'url' => ['/meet/create'],
				],
				[
					'label' => 'Kalendarz',
					'url' => ['/meet-calendar/index'],
				],
			],
		];

		$menuItems[] = [
			'label' => 'Sprawy',
			'url' => ['/issue/index'],
			'visible' => Yii::$app->user->can(User::PERMISSION_ISSUE),
		];
		$menuItems[] = [
			'label' => 'Znajdź sprawę',
			'url' => ['/issue/search'],
			'visible' => Yii::$app->user->can(User::ROLE_CUSTOMER_SERVICE),
		];
		$menuItems[] = [
			'label' => Yii::t('common', 'Summons'),
			'url' => ['/summon/index'],
			'visible' => Yii::$app->user->can(User::PERMISSION_SUMMON),
		];
		$menuItems[] = [
			'label' => 'Newsy',
			'url' => ['/article/index'],
		];
		$menuItems[] = [

			'label' => 'Prowizje',
			'url' => ['/report/index'],

		];
		$menuItems[] = [
			'label' => Yii::$app->user->identity->username,
			'url' => '#',
			'items' => [

				['label' => Yii::t('frontend', 'Settings'), 'url' => ['/account/default/settings']],
				['label' => 'Hierarchia', 'url' => ['/account/tree/index']],
				[
					'label' => Yii::t('frontend', 'Backend'),
					'url' => getenv('BACKEND_URL'),
					'linkOptions' => ['target' => '_blank'],
					'visible' => Yii::$app->user->can('manager', 'admnistrator'),
				],
				[
					'label' => Yii::t('frontend', 'Logout'),
					'url' => ['/account/sign-in/logout'],
					'linkOptions' => ['data-method' => 'post'],
				],
			],
		];
	}
	echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-right'],
		'items' => array_merge(NavItem::getMenuItems(), $menuItems),
	]);
	NavBar::end() ?>

	<div class="container">
		<?= Breadcrumbs::widget([
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>
		<?= Alert::widget() ?>
		<?= $content ?>

	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="pull-right"> All Rights Reserved 2016 &copy; - EdziMa</p>
	</div>
</footer>

<?php
yii\bootstrap\Modal::begin([
	'headerOptions' => ['id' => 'modalHeader'],
	'id' => 'modal',
	'size' => 'modal-lg',
	//keeps from closing modal with esc key or by clicking out of the modal.
	// user must click cancel or X to close

]);

echo "<div id='modalContent'></div>";
yii\bootstrap\Modal::end();
?>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


