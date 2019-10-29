<?php

namespace common\models\issue;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_pay".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $pay_at
 * @property string $deadline_at
 * @property string $value
 * @property int $type
 * @property int $transfer_type
 *
 * @property Issue $issue
 * @property-read IssuePay[] $pays
 */
class IssuePay extends ActiveRecord {

	public const TYPE_HONORARIUM = 1;
	public const TYPE_COMPENSTAION = 2;

	public const TRANSFER_TYPE_DIRECT = 1;
	public const TRANSFER_TYPE_BANK = 2;

	private static $PAYS = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return 'issue_pay';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['issue_id', 'type', 'value', 'deadline_at', 'transfer_type'], 'required','enableClientValidation' => false],
			[['issue_id', 'type', 'transfer_type'], 'integer'],
			[['pay_at', 'deadline_at'], 'safe'],
			[['value'], 'number'],
			[['type'], 'in', 'range' => array_keys(static::getTypesNames())],
			[['transfer_type'], 'in', 'range' => array_keys(static::getTransferTypesNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	public function afterSave($insert, $changedAttributes): void {
		$this->issue->markAsUpdate();
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete(): void {
		$this->issue->markAsUpdate();
		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'issue_id' => 'Sprawa',
			'pay_at' => 'Data płatności',
			'deadline_at' => 'Termin płatności',
			'value' => 'Kwota',
			'type' => 'Rodzaj',
			'transfer_type' => 'Przelew/konto',
			'partInfo' => 'Część',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public function getCityDate(): ?string {
		$details = $this->issue->payCity;
		if ($details === null) {
			return null;
		}
		switch ($this->transfer_type) {
			case static::TRANSFER_TYPE_DIRECT:
				return $details->direct_at;
			case static::TRANSFER_TYPE_BANK:
				return $details->bank_transfer_at;
			default:
				return null;
		}
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public function getTransferTypeName(): string {
		return static::getTransferTypesNames()[$this->transfer_type];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_HONORARIUM => 'Honorarium',
			static::TYPE_COMPENSTAION => 'Odszkodowanie',
		];
	}

	public static function getTransferTypesNames(): array {
		return [
			static::TRANSFER_TYPE_BANK => 'Przelew',
			static::TRANSFER_TYPE_DIRECT => 'Gotówka',
		];
	}

	public function isPayed(): bool {
		return $this->pay_at > 0;
	}

	public function markAsPay(): bool {
		if (!$this->isPayed()) {
			$this->updateAttributes(['pay_at' => date(DATE_ATOM)]);
			return true;
		}
		return false;
	}

	public function getPays(): array {
		if (!isset(static::$PAYS[$this->issue_id])) {
			static::$PAYS[$this->issue_id] = static::findAll(['issue_id' => $this->issue_id]);
		}
		return static::$PAYS[$this->issue_id];
	}

	public function getPartInfo(): string {
		$count = count($this->pays);
		if ($count === 1) {
			return '1/1';
		}
		$i = 0;
		foreach ($this->pays as $pay) {
			$i++;
			if ($pay->deadline_at === $this->deadline_at && $pay->value === $this->value) {
				return $i . '/' . $count;
			}
		}
		return '';
	}

	public static function find(): IssuePayQuery {
		return new IssuePayQuery(static::class);
	}

}
