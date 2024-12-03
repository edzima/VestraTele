<?php

namespace common\components;

use Closure;
use common\models\issue\Issue;
use common\models\issue\query\IssueQuery;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Exception;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class LeadsFromIssueCreator extends Component {

	/**
	 * @var string|array|Connection
	 */
	public $issueDb = 'db';
	/**
	 * @var string|array|Connection
	 */
	public $leadDb = 'db';
	public string $leadTable = '{{%lead}}';

	public int $issueBatchLimit = 100;

	public $leadsColumns = [
		'phone' => 'customer.phone',
		'name' => 'customer.fullName',
	];

	public array $requiredColumns = [
		'name',
		'phone',
	];

	private array $_leadColumnsNames = [];

	public function init(): void {
		parent::init();
		$this->issueDb = Instance::ensure($this->issueDb, Connection::class);
		$this->leadDb = Instance::ensure($this->leadDb, Connection::class);
	}

	/**
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function createLeads(IssueQuery $issueQuery): int {
		$count = 0;
		foreach ($issueQuery->batch($this->issueBatchLimit, $this->issueDb) as $issues) {
			$leadsData = [];
			foreach ($issues as $issue) {
				$data = $this->getLeadData($issue);
				$data = $this->validateLeadData($data);
				if (!empty($data)) {
					$leadsData[$issue->id] = $data;
				}
			}
			if (!empty($leadsData) && !empty($this->_leadColumnsNames)) {
				$count += $this->leadDb
					->createCommand()
					->batchInsert($this->leadTable, $this->_leadColumnsNames, $leadsData)
					->execute();
			}
		}
		return $count;
	}

	public function getLeadData(Issue $issue): array {
		if ($this->leadsColumns instanceof Closure) {
			return call_user_func($this->leadsColumns, $issue);
		}
		$data = [];
		foreach ($this->leadsColumns as $column => $attribute) {
			$data[$column] = $this->getIssueValue($issue, $attribute);
		}
		return $data;
	}

	protected function getIssueValue(Issue $issue, $attribute): ?string {
		if ($attribute instanceof Closure) {
			return call_user_func($attribute, $issue);
		}
		if (is_string($attribute)) {
			return ArrayHelper::getValue($issue, $attribute);
		}
		throw new InvalidConfigException('$attribute must be string or Closure: ' . $attribute);
	}

	private function validateLeadData(array $data): array {
		if (empty($this->_leadColumnsNames)) {
			$this->_leadColumnsNames = array_keys($data);
		}
		if ($this->_leadColumnsNames !== array_keys($data)) {
			throw new InvalidConfigException('All Leads data from issue must has same order key.');
		}
		foreach ($this->requiredColumns as $column) {
			if (!isset($data[$column])) {
				return [];
			}
		}
		return $data;
	}
}
