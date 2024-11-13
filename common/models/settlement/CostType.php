<?php

namespace common\models\settlement;

use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\helpers\ArrayHelper;
use common\models\issue\IssueCost;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "cost_type".
 *
 * @property int $id
 * @property string $name
 * @property int|null $is_active
 * @property int|null $is_for_settlement
 * @property string|null $options
 *
 * @property IssueCost[] $issueCosts
 */
class CostType extends ActiveRecord implements ModelRbacInterface {

	private ?CostTypeOptions $typeOptions = null;

	private static array $MODELS = [];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'cost_type';
	}

	/**
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getModels(bool $refresh = false): array {
		if (empty(static::$MODELS) || $refresh) {
			static::$MODELS = static::find()
				->indexBy('id')
				->all();
		}
		return static::$MODELS;
	}

	public static function getNames(bool $active, bool $refresh = false): array {
		$models = static::getModels($refresh);
		if ($active) {
			$models = array_filter($models, function (CostType $model) {
				return $model->is_active;
			});
		}
		return ArrayHelper::map($models, 'id', 'name');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['is_active', 'is_for_settlement'], 'integer'],
			[['options'], 'safe'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('settlement', 'ID'),
			'name' => Yii::t('settlement', 'Name'),
			'is_active' => Yii::t('settlement', 'Is Active'),
			'is_for_settlement' => Yii::t('settlement', 'Is for Settlement'),
			'options' => Yii::t('settlement', 'Options'),
		];
	}

	/**
	 * Gets query for [[IssueCosts]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssueCosts() {
		return $this->hasMany(IssueCost::class, ['type_id' => 'id']);
	}

	public function getTypeOptions(): CostTypeOptions {
		if ($this->typeOptions === null) {
			$attributes = $this->options;
			if (!is_array($attributes)) {
				$attributes = Json::decode($attributes);
			}
			$options = new CostTypeOptions($attributes);
			$this->typeOptions = $options;
		}
		return $this->typeOptions;
	}

	public function setTypeOptions(CostTypeOptions $model): void {
		$this->typeOptions = $model;
	}

	public function getRbacBaseName(): string {
		return 'settlement.cost-type';
	}

	public function getRbacId(): ?string {
		return $this->id;
	}

	public function getModelAccess(): ModelAccessManager {
		return Yii::$app->accessManagerFactory->getManager(
			static::class
		)->setModel($this);
	}
}
