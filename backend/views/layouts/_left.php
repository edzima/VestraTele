<?php

use backend\widgets\Menu;

/* @var $this \yii\web\View */
?>
<aside class="main-sidebar">
	<section class="sidebar">
		<?= Menu::widget([
			'options' => ['class' => 'sidebar-menu'],
			'items' => [
				[
					'label' => Yii::t('backend', 'Main'),
					'options' => ['class' => 'header'],
				],
				[
					'label' => Yii::t('backend', 'Users'),
					'url' => ['/user/index'],
					'icon' => '<i class="fa fa-users"></i>',
					'visible' => Yii::$app->user->can('administrator'),
				],
				[
					'label' => Yii::t('backend', 'Competition'),
					'url' => '#',
					'icon' => '<i class="fa fa-edit"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						['label' => Yii::t('backend', 'Articles'), 'url' => ['/article/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => Yii::t('backend', 'Article categories'), 'url' => ['/article-category/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					],
				],
				[
					'label' => 'Terytorium',
					'url' => '#',
					'icon' => '<i class="fa fa-home"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						['label' => 'Regiony', 'url' => ['/address/state/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => 'Powiaty', 'url' => ['/address/powiat/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => 'Miejscowości', 'url' => ['/address/city/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					],
				],
				[
					'label' => 'Spotkania',
					'url' => ['/task-status/index'],
					'icon' => '<i class="fa fa fa-suitcase"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						['label' => 'Spotkania', 'url' => ['/task/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => 'Raporty', 'url' => ['/task-status/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					],
				],
				[
					'label' => Yii::t('backend', 'Sprawy'),
					'url' => '#',
					'icon' => '<i class="fa fa-terminal"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						[
							'label' => Yii::t('issue', 'Przeglądaj'),
							'url' => ['/issue/issue/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Wpłaty'),
							'url' => ['/issue/pay/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Notatki'),
							'url' => ['/issue/note/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Podmioty odpowiedzialne'),
							'url' => ['/issue/entity-responsible/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Rodzaje'),
							'url' => ['/issue/type/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
						[
							'label' => Yii::t('issue', 'Etapy'),
							'url' => ['/issue/stage/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
						],
					],
				],
				[
					'label' => 'Punkty',
					'url' => ['/score/index'],
					'icon' => '<i class="fa fa-dot-circle-o"></i>',
				],
				[
					'label' => Yii::t('frontend', 'Calendar'),
					'url' => ['/calendar/index'],
					'icon' => '<i class="fa fa fa-calendar"></i>',
					'options' => ['class' => 'treeview'],
					'items' => [
						['label' => 'Przedstawiciele', 'url' => ['/calendar/agent'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
						['label' => 'Prawnicy', 'url' => ['/calendar/layer'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					],
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
						[
							'label' => Yii::t('backend', 'DB manager'),
							'url' => ['/db-manager/default/index'],
							'icon' => '<i class="fa fa-angle-double-right"></i>',
							'visible' => Yii::$app->user->can('administrator'),
						],
					],
				],
			],
		]) ?>
	</section>
</aside>
