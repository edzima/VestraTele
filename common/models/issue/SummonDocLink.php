<?php

namespace common\models\issue;

use yii\db\ActiveRecord;

/**
 * @property int $summon_id
 * @property int $doc_type_id
 * @property string|null $done_at
 *
 * @property-read Summon $summon
 * @property-read SummonDoc $summonDoc
 */
class SummonDocLink extends ActiveRecord {

	public static function tableName(): string {
		return '{{%summon_doc_list}}';
	}
}
