<?php

use yii\bootstrap\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
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
		[	'label' => Yii::t('frontend', 'Articles'),
			'url' => ['/article/index'],
			'visible' => Yii::$app->user->identity->isTele(),
		],
        [
            'label' => 'Ranking',
            'url' => ['/score'],
            'visible' => Yii::$app->user->identity->isTele(),
        ],
		[
            'label' => Yii::t('frontend', 'Twoje Spotkania'),
            'url' => ['/task'],
        ],
		[
            'label' => Yii::t('frontend', 'Spotkania'),
            'url' => ['/task-status'],
            'visible' => Yii::$app->user->identity->isAgent(),
        ],
        [
            'label' => Yii::t('frontend', 'Kalendarz'),
            'url' => ['/calendar/view?id=26'],
            'visible' => Yii::$app->user->identity->isTele(),
        ],
        [
            'label' => Yii::t('frontend', 'Osobisty kalendarz'),
            'url' => ['/calendar/agent?id='.Yii::$app->user->identity->id],
            'visible' => Yii::$app->user->identity->isAgent(),
        ],
		];

	   }


    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('frontend', 'Login'), 'url' => ['/account/sign-in/login']];
    } else {
        $menuItems[] = [

            'label' => Yii::$app->user->identity->username,
            'url' => '#',
            'items' => [

                ['label' => Yii::t('frontend', 'Settings'), 'url' => ['/account/default/settings']],
                [
                    'label' => Yii::t('frontend', 'Backend'),
                    'url' => getenv('BACKEND_URL'),
                    'linkOptions' => ['target' => '_blank'],
                    'visible' => Yii::$app->user->can('manager','admnistrator'),
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
