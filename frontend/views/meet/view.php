<?php

use common\modules\meet\widgets\MeetDetailView;
use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueMeet */

$this->title = $model->getClientFullName();
$this->params['breadcrumbs'][] = ['label' => 'Lead', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-meet-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
	</p>

	<?= MeetDetailView::widget([
		'model' => $model,
	]) ?>

</div>
