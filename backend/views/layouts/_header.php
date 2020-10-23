<?php

use yii\bootstrap\Html;

/* @var $this \yii\web\View */
?>
<header class="main-header">
	<?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg"><i class="fa fa-diamond"></i> ' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>
	<nav class="navbar navbar-static-top" role="navigation">
		<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
			<span class="sr-only">Toggle navigation</span>
		</a>
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">

				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<?php if (Yii::$app->user->identity->profile->avatar_path) : ?>
							<img src="<?= Yii::getAlias('@storageUrl/avatars/' . Yii::$app->user->identity->profile->avatar_path) ?>"
							     class="user-image" alt>
						<?php else: ?>
							<img src="<?= Yii::$app->homeUrl . '/static/img/default.png' ?>" class="user-image" alt>
						<?php endif ?>
						<span class="hidden-xs"><?= Yii::$app->user->identity->username ?></span>
					</a>
					<ul class="dropdown-menu">
						<li class="user-header">
							<?php if (Yii::$app->user->identity->profile->avatar_path) : ?>
								<img src="<?= Yii::getAlias('@storageUrl/avatars/' . Yii::$app->user->identity->profile->avatar_path) ?>"
								     class="img-circle" alt>
							<?php else: ?>
								<img src="<?= Yii::$app->homeUrl . '/static/img/default.png' ?>" class="img-circle" alt>
							<?php endif ?>
							<p>
								<?= Yii::$app->user->identity->username ?>
								<small><?= Yii::t('backend', 'Member since {0, date}', Yii::$app->user->identity->created_at) ?></small>
							</p>
						</li>
						<li class="user-footer">
							<div class="pull-left">
								<?= Html::a(
									Yii::t('backend', 'Profile'),
									['/user/update', 'id' => Yii::$app->user->id],
									['class' => 'btn btn-default btn-flat']
								) ?>
							</div>
							<div class="pull-right">
								<?= Html::a(
									Yii::t('backend', 'Logout'),
									['/site/logout'],
									[
										'id' => 'logout-link',
										'data-method' => 'post',
										'class' => 'btn btn-default btn-flat',
									]
								) ?>
							</div>
						</li>
					</ul>
				</li>
				<li>
					<?= Html::a(
						'<i class="fa fa-cogs"></i>',
						['/site/settings']
					) ?>
				</li>
			</ul>
		</div>
	</nav>
</header>
