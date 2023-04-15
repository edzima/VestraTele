<?php

use common\models\issue\IssueUser;
use common\widgets\grid\AgentDataColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use frontend\models\search\IssueCustomersSearch;
use frontend\widgets\GridView;
use frontend\widgets\IssueColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssueCustomersSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('frontend', 'Search Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
				'onlyUserLink' => true,
			],
			[
				'class' => IssueTypeColumn::class,
				'valueType' => IssueTypeColumn::VALUE_NAME,
			],
			[
				'class' => IssueStageColumn::class,
				'valueType' => IssueStageColumn::VALUE_NAME,
			],
			[
				'class' => AgentDataColumn::class,
				'value' => function (IssueUser $issueUser): string {
					$agent = $issueUser->issue->agent;
					if ($agent->id !== Yii::$app->user->getId()) {
						return Html::mailto(
							Html::encode($agent->getFullName()), $agent->email);
					}
					return Html::encode($agent->getFullName());
				},
				'format' => 'html',
			],
			[
				'attribute' => 'fullName',
				'label' => $searchModel->getAttributeLabel('surname'),
				'value' => 'user',
			],
			[
				'attribute' => 'phone',
				'value' => function (IssueUser $issueUser): ?string {
					if (Yii::$app->user->canSeeIssue($issueUser->issue)) {
						return $issueUser->user->getPhone();
					}
					return null;
				},
				'format' => 'tel',
				'label' => Yii::t('common', 'Phone number'),
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssueCustomersSearch::getTypesNames(),
			],
			[
				'attribute' => 'birthday',
				'format' => 'date',
				'label' => Yii::t('common', 'Birthday'),
				'value' => function (IssueUser $user): ?string {
					if ($user->isCustomerType()) {
						return $user->user->profile->birthday;
					}
					return null;
				},
			],

		],
	]); ?>
</div>
