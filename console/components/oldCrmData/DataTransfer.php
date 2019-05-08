<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-03
 * Time: 16:18
 */

namespace console\components\oldCrmData;

use Closure;
use console\components\oldCrmData\exceptions\InvalidModelData;
use console\components\oldCrmData\exceptions\OldIdColumnNotExistException;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BatchQueryResult;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;

abstract class DataTransfer extends Component {

	public $oldDb = 'oldDb';
	public $oldColumns = [];
	public $oldTableName = [];
	public $debug = true;
	/** @var ActiveRecord */
	public $model;
	public $addOldIdColumn = true;
	/** @var Closure */
	public $buildModel;
	/** @var Closure */
	public $queryCondition;
	public $batch = 100;
	public $removeAll = false;
	private $_isAddedOldIdColumn;
	private $_query;

	public function init() {
		if (!is_subclass_of($this->model, ActiveRecord::class)) {
			throw  new InvalidConfigException('$model must be sublass of ' . ActiveRecord::class);
		}
		if ($this->buildModel !== null && !$this->buildModel instanceof Closure) {
			throw new InvalidConfigException('$buildModel must be instance of ' . Closure::class);
		}
		if ($this->removeAll) {
			$this->model::deleteAll();
		}
		parent::init();
		$this->oldDb = Instance::ensure($this->oldDb, Connection::className());
	}

	public function transfer(): void {
		foreach ($this->getBatchResult() as $rows) {
			foreach ($rows as $row) {
				$this->transferModel($row);
			}
		}
	}

	/**
	 * @param array $row
	 * @throws InvalidModelData
	 * @throws OldIdColumnNotExistException
	 */
	public function transferModel(array $row): void {
		$this->checkOldIdColumn();
		$this->prepareModel($row);
	}

	/**
	 * @param array $row
	 * @throws InvalidModelData
	 */
	protected function prepareModel(array $row): void {
		if ($this->buildModel !== null) {
			$build = $this->buildModel;
			$build($row);
		} else {
			$this->createModel($row);
		}
	}

	/**
	 * @param array $row
	 * @throws InvalidModelData
	 */
	private function createModel(array $row): void {
		$model = $this->createNewModel();
		$model->setAttributes($this->getAttributesData($row));
		if (!$model->validate()) {
			throw new InvalidModelData($model, $row);
		}
		$model->save();
		$this->afterSaveModel($model, $row);
	}

	protected function createNewModel(): ActiveRecord {
		return new $this->model();
	}

	protected function afterSaveModel(ActiveRecord $model, array $row): void {

	}

	protected function getAttributesData(array $row): array {
		return $row;
	}

	private function checkOldIdColumn(): void {
		if ($this->addOldIdColumn && !$this->isAddedOldIdColumn()) {
			throw new OldIdColumnNotExistException($this->model::tableName());
		}
	}

	public function isAddedOldIdColumn(): bool {
		if ($this->_isAddedOldIdColumn === null) {
			$this->_isAddedOldIdColumn = $this->model::getTableSchema()->getColumn(Migration::OLD_ID_COLUMN_NAME) !== null;
		}
		return $this->_isAddedOldIdColumn;
	}

	/**
	 * @return BatchQueryResult
	 */
	public function getBatchResult(): BatchQueryResult {
		return $this->getQuery()->batch($this->batch, $this->oldDb);
	}

	private function getQuery(): Query {
		if ($this->_query === null) {
			$this->_query = new Query();
			$this->_query->from($this->oldTableName);
			if (!empty($this->oldColumns)) {
				$this->_query->select($this->oldColumns);
			}
			if ($this->queryCondition instanceof Closure) {
				$condition = $this->queryCondition;
				$condition($this->_query);
			}
		}
		return $this->_query;
	}

	protected function updateOldIdColumn(ActiveRecord $model, int $oldId): void {
		if ($model->{Migration::OLD_ID_COLUMN_NAME} === null) {
			$model->updateAttributes([Migration::OLD_ID_COLUMN_NAME => $oldId]);
		}
	}

	public function addOldIdColumn(): void {
		if (!$this->addOldIdColumn) {
			throw new InvalidConfigException(__FUNCTION__ . ' use only when $addOldIdColumn is true.');
		}
		$this->getMigration()->addOldIdColumn($this->model::tableName());
		$this->_isAddedOldIdColumn = true;
	}

	public function dropOldIdColumn(): void {
		if (!$this->addOldIdColumn) {
			throw new InvalidConfigException(__FUNCTION__ . ' use only when $addOldIdColumn is true.');
		}
		$this->getMigration()->dropOldIdColumn($this->model::tableName());
		$this->_isAddedOldIdColumn = false;
	}

	protected function getMigration(): Migration {
		return new Migration();
	}

	protected function message(string $message): void {
		if ($this->debug) {
			echo "   > $message\n";
		}
	}

}