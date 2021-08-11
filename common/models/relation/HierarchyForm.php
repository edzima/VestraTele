<?php

namespace common\models\relation;

use common\components\RelationComponent;
use common\models\user\UserRelation;
use Yii;
use yii\base\Model;
use yii\validators\CompareValidator;

class HierarchyForm extends Model {

	public $id;
	public $parent_id;

	public array $parentsMap = [];

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
			['id', 'compare', 'operator' => '!=', 'compareAttribute' => 'parent_id', 'type' => CompareValidator::TYPE_NUMBER],
			[
				'parent_id', 'in', 'not' => true, 'range' => function (): array {
				return $this->hierarchy->getAllChildesIds($this->id);
			},
				'message' => Yii::t('common', 'Parent cannot be from childes.'),
			],
			['id', 'exist', 'skipOnError' => true, 'targetClass' => $this->hierarchy->relationModel::toTargetClass(), 'targetAttribute' => ['id' => 'id']],
			['parent_id', 'exist', 'skipOnError' => true, 'targetClass' => $this->hierarchy->relationModel::fromTargetClass(), 'targetAttribute' => ['parent_id' => 'id']],
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
