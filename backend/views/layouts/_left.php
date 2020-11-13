<?php

use backend\widgets\Menu;
use common\models\user\User;

/* @var $this \yii\web\View */

$user = Yii::$app->user;
?>
<aside class="main-sidebar">
	<section class="sidebar">
		<?= Menu::widget([
			'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
			'items' => [
				[
					'label' => Yii::t('backend', 'Main'),
					'options' => ['class' => 'header'],
				],
				[
					'label' => Yii::t('backend', 'Users'),
					'url' => ['/user/user/index'],
					'icon' => '<i class="fa fa-users"></i>',
					'visible' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
				],
				[
					'label' => Yii::t('backend', 'Workers'),
					'url' => ['/user/worker/index'],
					'icon' => '<i class="fa fa-users"></i>',
					'visible' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
				],
				[
					'label' => Yii::t('backend', 'Customers'),
					'url' => ['/user/customer/index'],
					'icon' => '<i class="fa fa-users"></i>',
				],
				[
					'label' => Yii::t('backend', 'Meets'),
					'url' => '#',
					'icon' => '<i class="fa fa fa-calendar"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::PERMISSION_MEET),
					'items' => [
						[
							'label' => Yii::t('backend', 'Create'),
							'url' => ['/issue/meet/create'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('common', 'Browse'),
							'url' => ['/issue/meet/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => 'Kampanie',
							'url' => ['/campaign/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
					],
				],

				[
					'label' => Yii::t('common', 'Issues'),
					'url' => ['/issue/issue/index'],
					'icon' => '<i class="fa fa-suitcase"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::PERMISSION_ISSUE),
					'items' => [
						[
							'label' => Yii::t('common', 'Browse'),
							'url' => ['/issue/issue/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('common', 'Issues users'),
							'url' => ['/issue/user/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('common', 'Notes'),
							'url' => ['/issue/note/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => 'Podmioty',
							'url' => ['/entity-responsible/default/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],

						[
							'label' => Yii::t('common', 'Types'),
							'url' => ['/issue/type/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('common', 'Etapy'),
							'url' => ['/issue/stage/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],

						[
							'label' => Yii::t('backend', 'Costs'),
							'url' => ['/issue/cost/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::PERMISSION_COST),
						],
					],
				],
				[
					'label' => Yii::t('backend', 'Summons'),
					'url' => ['/issue/summon/index'],
					'icon' => '<i class="fa fa-angle-double-right"></i>',
					'visible' => $user->can(User::PERMISSION_SUMMON),
				],
				[
					'label' => Yii::t('backend', 'Provisions'),
					'url' => '#',
					'icon' => '<i class="fa fa-percent"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::ROLE_ADMINISTRATOR),
					'items' => [
						[
							'label' => Yii::t('issue', 'Raporty'),
							'url' => ['/provision/report/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Przyznane'),
							'url' => ['/provision/provision/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => 'Ustalone',
							'url' => ['/provision/user/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Typy'),
							'url' => ['/provision/type/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
					],
				],

				[
					'label' => Yii::t('backend', 'Płatności'),
					'url' => '#',
					'icon' => '<i class="fa fa-money"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::ROLE_BOOKKEEPER) || $user->can(User::PERMISSION_PAYS_DELAYED),
					'items' => [
						[
							'label' => Yii::t('issue', 'Rozliczenia (nowe)'),
							'url' => ['/issue/pay-calculation/new'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::ROLE_BOOKKEEPER),
						],
						[
							'label' => Yii::t('issue', 'Rozliczenia (w trakcie)'),
							'url' => ['/issue/pay-calculation/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::ROLE_BOOKKEEPER),
						],
						[
							'label' => Yii::t('issue', 'Wpłaty'),
							'url' => ['/issue/pay/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::ROLE_BOOKKEEPER),

						],
						[
							'label' => Yii::t('issue', 'Wpłaty (przeterminowane)'),
							'url' => ['/issue/pay/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::PERMISSION_PAYS_DELAYED),

						],
					],
				],
				[
					'label' => Yii::t('backend', 'Articles'),
					'url' => '#',
					'icon' => '<i class="fa fa-edit"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						['label' => Yii::t('backend', 'Articles'), 'url' => ['/article/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => Yii::t('backend', 'Article categories'), 'url' => ['/article-category/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					],
					'visible' => $user->can(User::PERMISSION_NEWS),
				],
				[
					'label' => Yii::t('backend', 'System'),
					'options' => ['class' => 'header'],
				],
				[
					'label' => Yii::t('backend', 'System'),
					'url' => '#',
					'icon' => '<i class="fa fa-terminal"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						['label' => Yii::t('backend', 'File manager'), 'url' => ['/file-manager/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => Yii::t('backend', 'Cache manager'), 'url' => ['/cache/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						[
							'label' => Yii::t('backend', 'Log manager'),
							'url' => ['/log/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::PERMISSION_LOGS),
						],
						[
							'label' => Yii::t('backend', 'DB manager'),
							'url' => ['/db-manager/default/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::ROLE_ADMINISTRATOR),
						],
					],
				],
			],
		]) ?>
	</section>
</aside>
