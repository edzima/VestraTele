<?php

use common\models\user\User;
use common\models\user\Worker;
use common\widgets\Alert;
use common\widgets\MultipleHostsButtonDropdown;
use frontend\assets\AppAsset;
use frontend\helpers\Html;
use frontend\models\NavItem;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/* @var $this View */
/* @var $content string */

AppAsset::register($this);
?>
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
			'label' => Yii::t('common', 'Potential Clients'),
			'url' => '#',
			'visible' => Yii::$app->user->can(Worker::PERMISSION_POTENTIAL_CLIENT),
			'items' => [
				[
					'label' => Yii::t('common', 'Search'),
					'url' => ['/potential-client/search'],
				],
				[
					'label' => Yii::t('common', 'Self'),
					'url' => ['/potential-client/self'],
				],

			],
		];
		$menuItems[] = [
			'label' => Yii::t('lead', 'Leads'),
			'url' => '#',
			'visible' => Yii::$app->user->can(User::PERMISSION_LEAD),
			'items' => [
				[
					'label' => Yii::t('common', 'Browse'),
					'url' => ['/lead/lead/index'],
				],
				[
					'label' => Yii::t('lead', 'Lead Markets'),
					'url' => ['/lead/market/user'],
					'visible' => Yii::$app->user->can(User::PERMISSION_LEAD_MARKET),
				],
				[
					'label' => Yii::t('lead', 'Create Lead'),
					'url' => ['/lead/lead/create'],
				],
				[
					'label' => Yii::t('lead', 'Lead Reports'),
					'url' => ['/lead/report/index'],
				],
				[
					'label' => Yii::t('lead', 'Reminders'),
					'url' => ['/lead/reminder/index'],
				],
				[
					'label' => Yii::t('lead', 'Campaigns'),
					'url' => ['/lead/campaign/index'],
				],
				[
					'label' => Yii::t('lead', 'Sources'),
					'url' => ['/lead/source/index'],
				],
			],
		];

		$menuItems[] = [
			'label' => Yii::t('hint', 'Hints'),
			'url' => '#',
			'visible' => Yii::$app->user->can(User::PERMISSION_HINT),
			'items' => [
				[
					'label' => Yii::t('hint', 'Hint Cities'),
					'url' => ['/hint-city/index'],
				],
				[
					'label' => Yii::t('hint', 'Hint Sources'),
					'url' => ['/hint-city-source/index'],
				],
			],
		];
		$menuItems[] = [
			'label' => Yii::t('court', 'Lawsuits'),
			'url' => ['/court/lawsuit/index'],
			'visible' => Yii::$app->user->can(Worker::PERMISSION_LAWSUIT),
		];

		$menuItems[] = [
			'label' => Yii::t('credit', 'Analyze SKD'),
			'url' => ['/credit/analyze/calc'],
			'visible' => Yii::$app->user->can(Worker::PERMISSION_CREDIT_ANALYZE),
		];
		$menuItems[] = [
			'label' => Yii::t('common', 'Issues'),
			'url' => ['/issue/index'],
			'visible' => Yii::$app->user->can(User::PERMISSION_ISSUE),
		];
		$menuItems[] = [
			'label' => Yii::t('common', 'Summons'),
			'url' => ['/summon/index'],
			'visible' => Yii::$app->user->can(User::PERMISSION_SUMMON),

			'items' => [
				[
					'label' => Yii::t('common', 'Browse'),
					'url' => ['/summon/index'],
				],
				[
					'label' => Yii::t('issue', 'Summon Docs'),
					'url' => ['/summon-doc/to-do'],
				],
				[
					'label' => 'Kalendarz',
					'url' => ['/calendar/summon-calendar/index'],
				],
			],
		];

		$menuItems[] = [
			'label' => Yii::t('frontend', 'Articles'),
			'url' => ['/article/index'],
			'items' => NavItem::getNewsCategoryItems(),
			'visible' => Yii::$app->user->can(User::PERMISSION_NEWS),
		];
		$menuItems[] = [
			'label' => 'Prowizje',
			'url' => ['/report/index'],

		];

		$multipleHosts = new MultipleHostsButtonDropdown();
		$hostsItems = $multipleHosts->defaultItems();
		if (!empty($hostsItems)) {
			$menuItems[] = [
				'label' => Html::icon('link'),
				'encode' => false,
				'url' => '#',
				'items' => $hostsItems,
			];
		}
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
		'id' => 'main-nav',
		'options' => ['class' => 'navbar-nav navbar-right'],
		'items' => $menuItems,
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
		<p class="pull-right"> All Rights Reserved <?= date('Y') ?> &copy; - EdziMa</p>
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


