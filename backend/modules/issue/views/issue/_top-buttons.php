<?php

use backend\helpers\Html;
use backend\widgets\CsvForm;
use common\models\user\Worker;
use yii\web\View;

/* @var $this View */
/* @var $parentTypeId int|null */

?>

<div class="issue-index-header-buttons">
	<div class="clearfix form-group">

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON)
			? Html::a(Yii::t('common', 'Summons'), ['/issue/summon/index', 'parentTypeId' => $parentTypeId], [
				'class' => 'btn btn-warning',
				'data-pjax' => 0,
			])
			: ''
		?>
		<?= Yii::$app->user->can(Worker::PERMISSION_NOTE)
			? Html::a(Yii::t('issue', 'Issue Notes'), ['note/index'], [
				'class' => 'btn btn-info',
				'data-pjax' => 0,
			])
			: ''
		?>
		<?= Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
			? Html::a(Yii::t('backend', 'Settlements'), ['/settlement/calculation/index'], [
				'class' => 'btn btn-success',
				'data-pjax' => 0,
			])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_STAGE_CHANGE)
			? Html::a('<i class="fa fa-calendar"></i>' . ' ' . Yii::t('issue', 'Stages Deadlines'),
				['/calendar/issue-stage-deadline/index', 'parentTypeId' => $parentTypeId ?? null,],
				[
					'class' => 'btn btn-warning',
					'data-pjax' => 0,
				])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_TAG_MANAGER)
			? Html::a(Html::icon('tags'), ['tag/index'],
				[
					'class' => 'btn btn-success',
					'title' => Yii::t('common', 'Tags'),
					'aria-label' => Yii::t('common', 'Tags'),
					'data-pjax' => 0,
				])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)
			? Html::a('<i class="fa fa-archive"></i>', ['archive/index'],
				[
					'class' => 'btn btn-danger',
					'title' => Yii::t('issue', 'Archive'),
					'aria-label' => Yii::t('issue', 'Archive'),
					'data-pjax' => 0,
				])
			: ''
		?>


		<span class="pull-right">

		<?= Yii::$app->user->can(Worker::PERMISSION_EXPORT)
			? CsvForm::widget([
				'formOptions' => ['class' => 'd-inline'],
			])
			: ''
		?>

		<?= Html::button(Html::icon('search'), [
			'data-toggle' => 'collapse',
			'data-target' => '#issue-search',
			'class' => 'btn btn-info',
		]) ?>

		</span>


	</div>
</div>
