<?php

use common\models\forms\HierarchyForm;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use common\models\user\Worker;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $user Worker */
/* @var $model HierarchyForm */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Set parent for: {user}', ['user' => $user->getFullName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-create user-worker-create">

	<?php $form = ActiveForm::begin() ?>

	<?= $form->field($model, 'parent_id')
		->widget(Select2::class, [
				'data' => $model->parentsMap,
				'options' => [
					'placeholder' => $model->getAttributeLabel('parent_id'),
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
			]
		)->hint(Yii::t('backend', 'Only agents')) ?>


	<?php

	if (!empty($model->getParentsIds())) {
		$dataProvider = new ActiveDataProvider([
			'query' => Worker::find()
				->andWhere(['id' => $model->getParentsIds()])
				->with('parent.userProfile')
				->with(['userProfile']),
			'pagination' => false,
		]);

		echo $this->render('_users', [
			'legend' => 'Przełożeni',
			'dataProvider' => $dataProvider,
		]);
	}

	if (!empty($model->getAllChildesIds())) {
		$dataProvider = new ActiveDataProvider([
			'query' => Worker::find()
				->andWhere(['id' => $model->getAllChildesIds()])
				->with('parent.userProfile')
				->with(['userProfile']),
			'pagination' => false,
		]);
		echo $this->render('_users', [
			'legend' => 'Podopieczni',
			'dataProvider' => $dataProvider,
		]);
	}

	?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
