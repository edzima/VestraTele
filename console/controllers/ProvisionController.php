<?php

namespace console\controllers;

use backend\modules\issue\models\search\IssueSearch;
use common\components\provision\exception\MissingProvisionUserException;
use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\provision\Provision;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class ProvisionController extends Controller {

	public bool $rollback = true;

	public function options($actionID): array {
		$options = parent::options($actionID);
		$options[] = 'rollback';
		return $options;
	}

	/**
	 * @return array{pay_id:string, to_user_id:string, from_user_id:string, type_id:string}
	 */
	private array $hidden = [];

	public function actionHidden(): void {
		$this->grabHidden();
		$this->updateHidden();
	}

	public function actionRefresh(): void {

		$this->grabHidden();
		$before = Provision::find()
			->groupBy(['to_user_id'])
			->select('to_user_id, SUM(value) as sum')
			->asArray()
			->all();

		$users = [];

		foreach ($before as $rows) {
			$user = User::findOne($rows['to_user_id']);
			$users[$rows['to_user_id']] = [
				'name' => $user->getFullName(),
				'before' => $rows['sum'],
			];
		}

		if ($this->rollback) {
			Yii::$app->db->beginTransaction();
		}
		Provision::deleteAll();
		foreach (IssuePayCalculation::find()
			->distinct()
			->withoutProvisions()
			->batch() as $rows) {
			foreach ($rows as $row) {
				$this->recalculateSettlement($row);
			}
		}

		$after = Provision::find()
			->groupBy(['to_user_id'])
			->select('to_user_id, SUM(value) as sum')
			->asArray()
			->all();
		foreach ($after as $rows) {
			$sum = $rows['sum'];
			$users[$rows['to_user_id']]['after'] = $sum;
			$before = $users[$rows['to_user_id']]['before'];
			$this->stdout($rows['name']);
			if ($before !== null) {
				$before = new Decimal($before);
				$sub = $before->sub($sum);
				$users[$rows['to_user_id']]['sub'] = Yii::$app->formatter->asCurrency($sub);
			}
		}

		$this->updateHidden();
		Console::output(print_r($users));
		if ($this->rollback) {
			Yii::$app->db->beginTransaction();
		}
	}

	public function actionIssues(int $userId): void {


		$provisions = [];
		$search = new IssueSearch();
		$search->parentId = $userId;
		$search->onlyWithSettlements = true;
		$dataProvider = $search->search([]);
		$dataProvider->pagination = false;
		Yii::$app->db->beginTransaction();

		$sumBefore = new Decimal(0);
		$sumAfter = new Decimal(0);
		foreach ($dataProvider->getModels() as $model) {

			/** @var Issue $model */
			foreach ($model->payCalculations as $calculation) {
				$sumBefore = $sumBefore->add($calculation->getProvisionsSum());
				Yii::$app->provisions->removeForPays($calculation->getPays()->getIds());
				try {
					Yii::$app->provisions->settlement($calculation);
					$calculation->refresh();
					$sumAfter = $sumAfter->add($calculation->getProvisionsSum());
				} catch (MissingProvisionUserException $exception) {
					Console::output($exception->getMessage());
				}
			}
		}
		Console::output($sumBefore);
		Console::output($sumAfter);

		Yii::$app->db->transaction->rollBack();
	}

	public function actionHierarchy(int $userId): void {
		$sumBefore = new Decimal(Provision::find()
			->andWhere(['from_user_id' => $userId])
			->orWhere(['to_user_id' => $userId])
			->sum('value')
		);

		$usersIds = Yii::$app->userHierarchy->getAllChildesIds($userId);
		$usersIds[] = $userId;
		Yii::$app->db->beginTransaction();

		$this->rollback = false;
		foreach ($usersIds as $id) {
			$this->actionRecalculate($id);
		}

		$sumAfter = new Decimal(Provision::find()
			->andWhere(['from_user_id' => $userId])
			->orWhere(['to_user_id' => $userId])
			->sum('value')
		);

		Console::output('Before: ' . Yii::$app->formatter->asCurrency($sumBefore));
		Console::output('After: ' . Yii::$app->formatter->asCurrency($sumAfter));

		Console::output('Sub: ' . Yii::$app->formatter->asCurrency(
				$sumBefore->sub($sumAfter)
			)
		);

		Yii::$app->db->transaction->rollBack();
	}

	public function actionRecalculate(int $userId): void {
		$user = User::findOne($userId);
		if (!$user) {
			Console::output('Not Found User with ID: ' . $userId);
		}
		Console::output(
			$user->getFullName()
		);
		$models = Provision::find()
			->andWhere(['from_user_id' => $userId])
			->orWhere(['to_user_id' => $userId])
			->all();

		if (empty($models)) {
			Console::output('Not Found Provision for User: ' . $userId);
			return;
		}

		$selfBefore = new Decimal(
			Provision::find()
				->user($userId)
				->sum('value')
		);

		Console::output('Find Provisions: ' . count($models));

		$sumBefore = new Decimal(0);
		foreach ($models as $model) {
			$sumBefore = $sumBefore->add($model->getValue());
		}

		if ($this->rollback) {
			Yii::$app->db->beginTransaction();
		}

		$this->recalculate($models);

		Console::output('---- FROM & TO PROVISIONS ----');

		$sumAfter = Provision::find()
			->andWhere(['from_user_id' => $userId])
			->orWhere(['to_user_id' => $userId])
			->sum('value');
		Console::output('All Before: ' . Yii::$app->formatter->asCurrency($sumBefore));
		Console::output('After: ' . Yii::$app->formatter->asCurrency($sumAfter));

		Console::output('Sub: ' . Yii::$app->formatter->asCurrency(
				$sumBefore->sub($sumAfter)
			)
		);

		Console::output('---- ONLY TO USER PROVISIONS  ----');

		$selfAfter = new Decimal(
			Provision::find()
				->user($userId)
				->sum('value')
		);

		Console::output('Before: ' . Yii::$app->formatter->asCurrency($selfBefore));
		Console::output('After: ' . Yii::$app->formatter->asCurrency($selfAfter));

		Console::output('Sub: ' . Yii::$app->formatter->asCurrency(
				$selfBefore->sub($selfAfter)
			)
		);

		if ($this->rollback) {
			Yii::$app->db->transaction->rollBack();
		}
		Console::output("\n");
	}

	public function actionDoublesSum(): void {
		Console::output(
			print_r(

				Provision::find()
					->select('*, SUM(value), COUNT(*)')
					->groupBy('pay_id, to_user_id, from_user_id')
					->having('COUNT(*) > 1')
					->asArray()
					->all()

			)
		);
	}

	public function actionDoubles(int $userId = null): void {
		$models = Provision::find()
			->select('*, COUNT(*)')
			->groupBy('pay_id, to_user_id, from_user_id')
			->andFilterWhere(['to_user_id' => $userId])
			->having('COUNT(*) > 1')
			->all();

		Console::output('Find Doubles Provision: ' . count($models));
		if (!empty($models)) {
			$usersSum = [];
			$usersIds = array_unique(ArrayHelper::getColumn($models, 'to_user_id'));
			$users = User::getSelectList($usersIds, false);
			Console::output('Users: ');
			Console::output(print_r($users));

			foreach ($users as $id => $name) {
				$usersSum[$id]['name'] = $name;
				$usersSum[$id]['before'] = Provision::find()->user($id)->sum('value');
			}

			if ($this->rollback) {
				Yii::$app->db->beginTransaction();
			}

			$this->recalculate($models);

			foreach ($users as $id => $name) {
				$usersSum[$id]['after'] = Provision::find()->user($id)->sum('value');
				$usersSum[$id]['diff'] = Yii::$app->formatter->asCurrency($usersSum[$id]['before'] - $usersSum[$id]['after']);
			}

			Console::output(print_r($usersSum));
			if ($this->rollback) {
				Yii::$app->db->transaction->rollBack();
			}
		}
	}

	/**
	 * @param Provision[] $provisions
	 * @return IssuePayCalculation[]
	 */
	private function recalculate(array $provisions): array {
		$settlements = [];
		foreach ($provisions as $model) {
			if (!isset($settlements[$model->pay->calculation_id])) {
				//	Console::output($model->getIssueName());
				$settlements[$model->pay->calculation_id] = $model->pay->calculation;
			}
		}
		Console::output('Find Settlements to Recalculate: ' . count($settlements));

		foreach ($settlements as $settlement) {
			$this->recalculateSettlement($settlement);
		}
		return $settlements;
	}

	private function recalculateSettlement(IssueSettlement $model) {
		Yii::$app->provisions->removeForPays($model->getPays()->getIds());
		try {
			Yii::$app->provisions->settlement($model);
		} catch (MissingProvisionUserException $exception) {
			Console::output($exception->getMessage());
		}
	}

	private function grabHidden(): void {
		$this->hidden = Provision::find()
			->select(['pay_id', 'to_user_id', 'from_user_id', 'type_id'])
			->hidden()
			->asArray()
			->all();
		Console::output('Find Hidden Provisions: ' . count($this->hidden));
	}

	private function updateHidden(): void {
		$paysIds = ArrayHelper::getColumn($this->hidden, 'pay_id');
		if (!empty($paysIds)) {
			$provisions = Provision::find()
				->select(['id', 'pay_id', 'type_id', 'from_user_id', 'to_user_id'])
				->andWhere(['pay_id' => $paysIds])
				->asArray()
				->all();
			$toHide = [];
			foreach ($provisions as $provision) {
				foreach ($this->hidden as $hide) {
					if (
						$provision['pay_id'] === $hide['pay_id']
						&& $provision['type_id'] === $hide['type_id']
						&& $provision['from_user_id'] === $hide['from_user_id']
						&& $provision['to_user_id'] === $hide['to_user_id']
					) {
						$toHide[] = $provision['id'];
					}
				}
			}

			Console::output('Find Provisions to Hide: ' . count($toHide));

			if (!empty($toHide)) {
				Console::output('Update Provisions as Hidden: ' . Provision::updateAll(['hide_on_report' => 1], ['id' => $toHide]));
			}
		}
	}

}
