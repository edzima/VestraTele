<?php

namespace common\models\forms;

use common\components\HierarchyComponent;
use common\models\hierarchy\HierarchyModel;
use Yii;
use yii\base\Model;

class HierarchyForm extends Model {

	public $id;
	public $parent_id;

	public array $parentsMap = [];

	public ?HierarchyModel $model = null;

	private HierarchyComponent $hierarchy;

	public function __construct(HierarchyComponent $hierarchy, $config = []) {
		$this->hierarchy = $hierarchy;
		parent::__construct($config);
	}

	public function getModel(): HierarchyModel {
		if ($this->model === null) {
			$this->model = $this->hierarchy->getModel($this->id);
		}
		return $this->model;
	}

	public function attributeLabels() {
		return [
			'id' => 'ID',
			'parent_id' => Yii::t('common', 'Parent'),
		];
	}

	public function rules(): array {
		return [
			[['id'], 'required'],
			['id', 'exist', 'skipOnError' => true, 'targetAttribute' => [$this->hierarchy->primaryKeyColumn => 'id']],
			['parent_id', 'exist', 'skipOnError' => true, 'targetClass' => $this->hierarchy->modelClass, 'targetAttribute' => [$this->hierarchy->parentColumn => 'id']],
		];
	}

	public function getParentsIds(): array {
		return $this->hierarchy->getParentsIds($this->id);
	}

	public function getAllChildesIds(): array {
		return $this->hierarchy->getAllChildesIds($this->id);
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		return $this->hierarchy->assign($this->id, $this->parent_id);
	}

}
