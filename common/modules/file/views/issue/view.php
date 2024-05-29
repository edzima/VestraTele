<?php

use backend\helpers\Breadcrumbs;
use common\helpers\Html;
use common\modules\file\models\File;
use common\modules\file\models\IssueFile;
use yii\widgets\DetailView;
use common\models\user\Worker;

/* @var $this yii\web\View */
/* @var $model IssueFile */

$this->title = Yii::t('file', 'File {name} in Issue: {issue}', [
		'issue' => $model->issue->getIssueName(),
		'name' => $model->file->name,
	]
);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
$this->params['breadcrumbs'][] = $model->file->name;
?>

<div class="issue-file-view">
	<p>
		<?= Html::a(Yii::t('common', 'Update'), ['update', 'file_id' => $model->file_id, 'issue_id' => $model->issue_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Delete'), ['delete', 'file_id' => $model->file_id, 'issue_id' => $model->issue_id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('issue', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model->file,
				'attributes' => [
					[
						'attribute' => 'typeName',
						'format' => 'html',
						'value' => function (File $file): string {
							$value = Html::encode($file->getTypeName());
							if (Yii::$app->user->can(Worker::PERMISSION_FILE_TYPE)) {
								return Html::a($value, ['file-type/view', 'id' => $file->file_type_id]);
							}
							return $value;
						},
					],
					'mime',
					'path',
					'owner',
					'created_at:datetime',
				],
			]) ?>
		</div>

		<div class="col-md-4">
			<p>
				<?= Html::a(Yii::t('file', 'Add Access'),
					['access', 'file_id' => $model->file_id, 'issue_id' => $model->issue_id],
					['class' => 'btn btn-success'])
				?>
			</p>

			<?= $this->render('_access_grid', ['model' => $model,]) ?>
		</div>
	</div>

</div>
