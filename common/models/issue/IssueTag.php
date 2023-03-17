<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%issue_tag}}".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int|null $type
 * @property int|null $is_active
 *
 * @property IssueTagType|null $tagType
 * @property IssueTagLink[] $issueTagLinks
 */
class IssueTag extends ActiveRecord {

	public const TYPE_CLIENT = 'client';
	public const TYPE_SETTLEMENT = 'settlement';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_tag}}';
	}

	/**
	 * @param static[] $tags
	 * @param string $type
	 * @return static[]
	 */
	public static function typeFilter(array $tags, string $type = null): array {
		$models = [];
		foreach ($tags as $tag) {
			if ($tag->type === $type) {
				$models[$tag->id] = $tag;
			}
		}
		return $models;
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_CLIENT => Yii::t('issue', 'Client'),
			static::TYPE_SETTLEMENT => Yii::t('issue', 'Settlement'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			['name', 'unique'],
			[['is_active'], 'integer'],
			[['type'], 'string'],
			[['type'], 'default', 'value' => null],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[['name', 'description'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('issue', 'ID'),
			'name' => Yii::t('issue', 'Name'),
			'description' => Yii::t('issue', 'Description'),
			'is_active' => Yii::t('issue', 'Is Active'),
			'type' => Yii::t('issue', 'Type'),
			'typeName' => Yii::t('issue', 'Type'),
		];
	}

	public function getTypeName(): ?string {
		return static::getTypesNames()[$this->type] ?? null;
	}

	/**
	 * Gets query for [[IssueTagLinks]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssueTagLinks(): ActiveQuery {
		return $this->hasMany(IssueTagLink::class, ['tag_id' => 'id']);
	}
}
