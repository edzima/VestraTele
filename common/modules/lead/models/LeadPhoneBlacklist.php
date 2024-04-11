<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_phone_blacklist".
 *
 * @property string $phone
 * @property int $user_id
 * @property int $status
 * @property string $created_at
 *
 * @property Lead[] $leads
 * @property LeadUserInterface|null $user
 */
class LeadPhoneBlacklist extends ActiveRecord {

	public static function tableName(): string {
		return '{{%lead_phone_blacklist}}';
	}

	public function rules(): array {
		return [
			[['phone'], 'required'],
			[['phone'], 'string'],
			['phone', 'unique'],
			[['user_id'], 'integer'],
		];
	}

	public function getLeads(): ActiveQuery {
		return $this->hasMany(Lead::class, ['phone' => 'phone']);
	}

	public function getUser(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'user_id']);
	}
}
