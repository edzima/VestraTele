<?php

use common\helpers\Html;
use common\modules\file\models\FileAccess;
use common\modules\file\models\IssueFile;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;

/**
 * @var IssueFile $model
 */

?>

<?= GridView::widget([
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->file->getFileAccess(),
	]),
	'columns' => [
		'user',
		[
			'label' => Yii::t('file', 'Revoke Access'),
			'format' => 'raw',
			'value' => function (FileAccess $access) use ($model) {
				return
					Html::a(
						Html::icon('remove'),
						[
							'revoke-access',
							'file_id' => $access->file_id,
							'user_id' => $access->user_id,
							'issue_id' => $model->issue_id,
						], [
						'class' => 'text-danger text-center',
						'data-method' => 'POST',
						'data-confirm' => Yii::t('file', 'Are you sure you want to revoke access?'),
					]);
			},
		],
	],
]) ?>
