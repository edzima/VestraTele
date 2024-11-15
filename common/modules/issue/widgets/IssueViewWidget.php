<?php

namespace common\modules\issue\widgets;

use common\components\rbac\CostTypeAccessManager;
use common\helpers\Url;
use common\models\user\Worker;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class IssueViewWidget extends IssueWidget {

	public bool $usersLinks = true;
	public bool $userMailVisibilityCheck = false;
	public bool $relationActionColumn = true;
	public bool $claimActionColumn = true;

	public bool $shipmentsActionColumn = false;
	public bool $lawsuitActionColumn = false;

	public ?string $entityResponsibleRoute = null;

	private ?string $entityUrl = null;

	public ?string $typeRoute = null;

	public ?string $costRoute = null;

	private ?string $typeUrl = null;

	public ?string $stageRoute = null;

	private ?string $stageUrl = null;

	public function init() {
		parent::init();
		if ($this->entityResponsibleRoute !== null) {
			$this->entityUrl = Url::to([$this->entityResponsibleRoute, 'id' => $this->model->entity_responsible_id]);
		}
		if ($this->stageRoute !== null && Yii::$app->user->can(Worker::PERMISSION_ISSUE_STAGE_MANAGER)) {
			$this->stageUrl = Url::to([$this->stageRoute, 'id' => $this->model->stage_id]);
		}
		if ($this->typeRoute !== null && Yii::$app->user->can(Worker::PERMISSION_ISSUE_TYPE_MANAGER)) {
			$this->typeUrl = Url::to([$this->typeRoute, 'id' => $this->model->type_id]);
		}
	}

	public function run(): string {
		return $this->render('issue-view', [
			'model' => $this->model,
			'userMailVisibilityCheck' => $this->userMailVisibilityCheck,
			'usersLinks' => $this->usersLinks,
			'claimActionColumn' => $this->claimActionColumn,
			'lawsuitActionColumn' => $this->lawsuitActionColumn,
			'relationActionColumn' => $this->relationActionColumn,
			'shipmentsActionColumn' => $this->shipmentsActionColumn,
			'costRoute' => $this->costRoute,
			'entityUrl' => $this->entityUrl,
			'stageUrl' => $this->stageUrl,
			'typeUrl' => $this->typeUrl,
			'costDataProvider' => $this->getCostDataProvider(),
		]);
	}

	protected function getCostDataProvider(): ?DataProviderInterface {
		$costDataProvider = null;
		if (Yii::$app->user->can(Worker::PERMISSION_COST)) {
			$query = $this->model->getCosts()
				->with('user.userProfile')
				->userAccessTypes(Yii::$app->user->getId(), CostTypeAccessManager::ACTION_ISSUE_VIEW);
			$costDataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
		}
		return $costDataProvider;
	}
}
