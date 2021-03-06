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
					'label' => Yii::t('common', 'Leads'),
					'url' => ['/lead/lead/index'],
					'visible' => $user->can(User::PERMISSION_LEAD),
					'icon' => '<i class="fa fa-vcard"></i>',
					'items' => [
						[
							'label' => Yii::t('lead', 'Browse'),
							'url' => ['/lead/lead/index'],
							'icon' => '<i class="fa fa-eye"></i>',
						],
						[
							'label' => Yii::t('lead', 'Reports'),
							'url' => ['/lead/report/index'],
							'icon' => '<i class="fa fa-table"></i>',
						],
						[
							'label' => Yii::t('common', 'Reminders'),
							'url' => ['/lead/reminder/index'],
							'icon' => '<i class="fa fa-table"></i>',
						],
						[
							'label' => Yii::t('lead', 'Answers'),
							'url' => ['/lead/answer/index'],
							'icon' => '<i class="fa fa-table"></i>',
						],
						[
							'label' => Yii::t('lead', 'Questions'),
							'url' => ['/lead/question/index'],
							'icon' => '<i class="fa fa-table"></i>',
						],
						[
							'label' => Yii::t('lead', 'Users'),
							'url' => ['/lead/user/index'],
							'icon' => '<i class="fa fa-users"></i>',
						],
						[
							'label' => Yii::t('lead', 'Campaigns'),
							'url' => ['/lead/campaign/index'],
							'icon' => '<i class="fa fa-eye"></i>',
						],
						[
							'label' => Yii::t('lead', 'Sources'),
							'url' => ['/lead/source/index'],
							'icon' => '<i class="fa fa-eye"></i>',
						],
						[
							'label' => Yii::t('lead', 'Statuses'),
							'url' => ['/lead/status/index'],
							'icon' => '<i class="fa fa-eye"></i>',
						],
						[
							'label' => Yii::t('lead', 'Types'),
							'url' => ['/lead/type/index'],
							'icon' => '<i class="fa fa-eye"></i>',
						],

					],
				],
				[
					'label' => Yii::t('hint', 'Hints'),
					'url' => ['/hint/city/index'],
					'icon' => '<i class="fa fa fa-map-marker"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::PERMISSION_HINT),
					'items' => [
						[
							'label' => Yii::t('hint', 'Hint Cities'),
							'url' => ['/hint/city/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('hint', 'Hint Cities Sources'),
							'url' => ['/hint/city-source/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('hint', 'Hint Sources'),
							'url' => ['/hint/source/index'],
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
					],
				],
				[
					'label' => Yii::t('backend', 'Summons'),
					'url' => ['/issue/summon/index'],
					'icon' => '<i class="fa fa-bolt"></i>',
					'visible' => $user->can(User::PERMISSION_SUMMON),
				],

				[
					'label' => Yii::t('backend', 'Settlements'),
					'url' => '#',
					'icon' => '<i class="fa fa-money"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::PERMISSION_COST)
						|| $user->can(User::PERMISSION_CALCULATION_TO_CREATE)
						|| $user->can(User::PERMISSION_CALCULATION_PROBLEMS)
						|| $user->can(User::PERMISSION_PAY)
						|| $user->can(User::PERMISSION_PAY_RECEIVED)
						|| $user->can(User::PERMISSION_PAYS_DELAYED)
					,
					'items' => [
						[
							'label' => Yii::t('backend', 'Browse'),
							'url' => ['/settlement/calculation/index'],
							'icon' => '<i class="fa fa-money"></i>',
							'visible' => $user->can(User::ROLE_BOOKKEEPER),
						],
						[
							'label' => Yii::t('settlement', 'Without provisions'),
							'url' => ['/settlement/calculation/without-provisions'],
							'icon' => '<i class="fa fa-calculator"></i>',
							'visible' => $user->can(User::PERMISSION_PROVISION),
						],
						[
							'label' => Yii::t('backend', 'Calculation to create'),
							'url' => ['/settlement/calculation/to-create'],
							'icon' => '<i class="fa fa-pencil"></i>',
							'visible' => $user->can(User::PERMISSION_CALCULATION_TO_CREATE),
						],
						[
							'label' => Yii::t('settlement', 'Uncollectible'),
							'url' => ['/settlement/calculation-problem/index'],
							'icon' => '<i class="fa fa-bolt"></i>',
							'visible' => $user->can(User::PERMISSION_CALCULATION_PROBLEMS),
						],
						[
							'label' => Yii::t('settlement', 'Pays'),
							'url' => ['/settlement/pay/index'],
							'icon' => '<i class="fa fa-plus"></i>',
							'visible' => $user->can(User::PERMISSION_PAY),
						],
						[
							'label' => Yii::t('settlement', 'Delayed pays'),
							'url' => ['/settlement/pay/delayed'],
							'icon' => '<i class="fa fa-plus"></i>',
							'visible' => $user->can(User::PERMISSION_PAYS_DELAYED),
						],
						[
							'label' => Yii::t('settlement', 'Received pays'),
							'url' => ['/settlement/pay-received/index'],
							'icon' => '<i class="fa fa-car"></i>',
							'visible' => $user->can(User::PERMISSION_PAY_RECEIVED),
						],
						[
							'label' => Yii::t('settlement', 'Costs'),
							'url' => ['/settlement/cost/index'],
							'icon' => '<i class="fa fa-minus"></i>',
							'visible' => $user->can(User::PERMISSION_COST),
						],
					],
				],
				[
					'label' => Yii::t('backend', 'Provisions'),
					'url' => '#',
					'icon' => '<i class="fa fa-percent"></i>',
					'options' => ['class' => 'treeview'],
					'visible' => $user->can(User::PERMISSION_PROVISION),
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
					'label' => Yii::t('backend', 'Addresses'),
					'url' => ['/address/index'],
					'icon' => '<i class="fa fa-home"></i>',
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
						[
							'label' => Yii::t('backend', 'Min calculation count'),
							'url' => ['/settlement/calculation-min-count/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => $user->can(User::PERMISSION_CALCULATION_TO_CREATE),
						],
					],
				],
			],
		]) ?>
	</section>
</aside>
