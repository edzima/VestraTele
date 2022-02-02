<?php

use common\models\user\User;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\models\NavItem;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
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
			'label' => Yii::t('lead', 'Leads'),
			'url' => '#',
			'visible' => Yii::$app->user->can(User::PERMISSION_LEAD),
			'items' => [
				[
					'label' => Yii::t('common', 'Browse'),
					'url' => ['/lead/lead/index'],
				],
				[
					'label' => Yii::t('lead', 'Calendar'),
					'url' => ['/calendar/lead/index'],
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
			'label' => Yii::t('common', 'Issues'),
			'url' => ['/issue/index'],
			'visible' => Yii::$app->user->can(User::PERMISSION_ISSUE),
		];
		$menuItems[] = [
			'label' => Yii::t('common', 'Summons'),
			'url' => ['/summon/index'],
			'visible' => Yii::$app->user->can(User::PERMISSION_SUMMON),
		];
		$menuItems[] = [
			'label' => Yii::t('frontend', 'Articles'),
			'url' => ['/article/index'],
			'items' => NavItem::getNewsCategoryItems(),
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


