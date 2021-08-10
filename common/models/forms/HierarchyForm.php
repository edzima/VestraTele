<?php

namespace common\models\forms;

use common\components\HierarchyComponent;
use common\models\hierarchy\ActiveHierarchy;
use common\components\RelationComponent;
use common\models\hierarchy\Hierarchy;
use common\models\user\UserRelation;
use Yii;
use yii\base\Model;

class HierarchyForm extends Model {

	public $id;
	public $parent_id;

	public array $parentsMap = [];

	//@todo check this model is use.
	public ?Hierarchy $model = null;

	private RelationComponent $hierarchy;

	public function __construct(RelationComponent $hierarchy, $config = []) {
		$this->hierarchy = $hierarchy;
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'parent_id' => Yii::t('common', 'Parent'),
		];
	}

	public function rules(): array {
		return [
			[['id'], 'required'],
			['id', 'exist', 'skipOnError' => true, 'targetClass' => $this->hierarchy->modelClass, 'targetAttribute' => ['id' => 'id']],
			['parent_id', 'exist', 'skipOnError' => true, 'targetClass' => $this->hierarchy->modelClass, 'targetAttribute' => ['id' => 'id']],
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
		$this->hierarchy->unassign(UserRelation::TYPE_SUPERVISOR, null, $this->id);
		if (!empty($this->parent_id)) {
			$this->hierarchy->assign(UserRelation::TYPE_SUPERVISOR, $this->parent_id, $this->id);
		}
		return true;
		if (empty($this->parent_id)) {
			return $this->hierarchy->unassign(UserRelation::TYPE_SUPERVISOR, $this->id);
		}
		return $this->hierarchy->assign(UserRelation::TYPE_SUPERVISOR, $this->id, $this->parent_id);
	}

}
