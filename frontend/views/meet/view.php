<?php

use common\models\User;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueMeet */

$this->title = $model->getClientFullName();
$this->params['breadcrumbs'][] = ['label' => 'Spotkanie', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-meet-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= $model->isForUser(Yii::$app->user->getId()) ? Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'type',
			[
				'attribute' => 'campaign',
				'visible' => Yii::$app->user->can(User::ROLE_TELEMARKETER),
			],
			'client_name',
			'client_surname',
			'phone',
			'tele',
			'agent',
			'state',
			'province',
			'subProvince',
			'city',
			'street',
			'created_at:datetime',
			'updated_at:datetime',
			'date_at:datetime',
			'details:ntext',
			'statusName',
		],
	]) ?>

</div>
