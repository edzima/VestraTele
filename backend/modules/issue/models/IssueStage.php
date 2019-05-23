<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-05-09
 * Time: 14:12
 */

namespace backend\modules\issue\models;

use common\models\issue\IssueStage as BaseIssueStage;
use common\models\issue\IssueType;
use yii\helpers\ArrayHelper;

class IssueStage extends BaseIssueStage {

	public $typesIds = [];

	public function afterFind() {
		parent::afterFind();
		$this->typesIds = ArrayHelper::map($this->types, 'id', 'id');
	}

	public function beforeSave($insert) {
		if (!$insert) {
			$this->unlinkAll('types', true);
			foreach ($this->typesIds as $typeId) {
				$this->link('types', IssueType::get($typeId));
			}
		}

		return parent::beforeSave($insert);
	}



}