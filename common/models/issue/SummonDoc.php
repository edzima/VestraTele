<?php

namespace common\models\issue;

use common\helpers\Html;
use common\models\issue\query\SummonDocQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "summon_doc".
 *
 * @property int $id
 * @property string $name
 * @property int|null $priority
 * @property string|null $summon_types
 *
 * @property Summon[] $summons
 * @property-read string|null $priorityName
 */
class SummonDoc extends ActiveRecord {

	public const PRIORITY_LOW = 1;
	public const PRIORITY_MEDIUM = 5;
	public const PRIORITY_HIGH = 10;

	public function getPriorityName(): ?string {
		return static::getPriorityNames()[$this->priority];
	}

	public static function getPriorityNames(): array {
		return [
			static::PRIORITY_HIGH => Yii::t('common', 'High'),
			static::PRIORITY_MEDIUM => Yii::t('common', 'Medium'),
			static::PRIORITY_LOW => Yii::t('common', 'Low'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon_doc}}';
	}

	public static function getNames(int $summonTypeId = null): array {
		$query = static::find();
		$models = $query->all();
		if ($summonTypeId !== null) {
			$models = array_filter($models, function (SummonDoc $doc) use ($summonTypeId): bool {
				return $doc->isForSummonType($summonTypeId);
			});
		}
		return ArrayHelper::map($models, 'id', 'name');
	}

	public function isForSummonType(int $typeId): bool {
		$typesIds = $this->getSummonTypesIds();
		return empty($typesIds) || in_array($typeId, $typesIds);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			['done_at', 'safe'],
			['priority', 'integer'],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('common', 'ID'),
			'name' => Yii::t('common', 'Name'),
			'priority' => Yii::t('common', 'Priority'),
			'priorityName' => Yii::t('common', 'Priority'),
			'summonTypesNames' => Yii::t('issue', 'Summon Types'),
		];
	}

	/**
	 * Gets query for [[Summons]].
	 *
	 * @return ActiveQuery
	 */
	public function getSummons() {
		return $this->hasMany(Summon::class, ['id' => 'summon_id'])->via(SummonDocLink::class, ['doc_type_id' => 'id']);
	}

	public function getLinks() {
		return $this->hasMany(SummonDocLink::class, ['doc_type_id' => 'id']);
	}

	public function getSummonTypesNames(bool $html = false): string {
		$ids = $this->getSummonTypesIds();
		if (empty($ids)) {
			return Yii::t('common', 'All');
		}
		$names = [];
		foreach ($ids as $id) {
			$name = SummonType::getNames()[$id] ?? null;
			if ($name) {
				if ($html) {
					$name = Html::encode($name);
				}
				$names[] = $name;
			}
		}
		if ($html) {
			return Html::ul($names);
		}
		return implode(', ', $names);
	}

	public function getSummonTypesIds(): array {
		if (empty($this->summon_types)) {
			return [];
		}
		return explode('|', $this->summon_types);
	}

	public function setSummonTypesIds(array $ids) {
		if (empty($ids)) {
			$this->summon_types = null;
		} else {
			$this->summon_types = implode('|', $ids);
		}
	}

	public static function find(): SummonDocQuery {
		return (new SummonDocQuery(static::class))
			->orderBy([
				static::tableName() . '.priority' => SORT_DESC,
				static::tableName() . '.name' => SORT_ASC,
			]);
	}

}
