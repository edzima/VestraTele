<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\models\NavItem;
use lo\modules\noty\Wrapper;

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
	<?= Html::csrfMetaTags() ?>
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

	if (!Yii::$app->user->isGuest) {

		$menuItems = [
			[
				'label' => Yii::t('frontend', 'Your tasks'),
				'url' => ['/task'],
			],
		];

		if (Yii::$app->user->can('telemarketer')) {
			$menuItems = [
				[
					'label' => Yii::t('frontend', 'Articles'),
					'url' => ['/article/index'],
				],
				[
					'label' => 'Ranking',
					'url' => ['/score'],
				],

				[
					'label' => Yii::t('frontend', 'Calendar'),
					'url' => ['/calendar/view?id=26'],
				],
			];
		}

		if (Yii::$app->user->can('agent')) {
			$menuItems = [
				[
					'label' => Yii::t('frontend', 'Task'),
					'url' => ['/task-status'],
				],
				/*
				[
					'label' => Yii::t('frontend', 'Your calendar'),
					'url' => ['/calendar/agent?id=' . Yii::$app->user->identity->id],
				],
				*/
			];
		}
	}

	if (Yii::$app->user->isGuest) {
		$menuItems[] = ['label' => Yii::t('frontend', 'Login'), 'url' => ['/account/sign-in/login']];
	} else {
		$menuItems[] = [
			'label' => 'Sprawy',
			'url' => ['/issue/index'],
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
		<?= Wrapper::widget() ?>
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


