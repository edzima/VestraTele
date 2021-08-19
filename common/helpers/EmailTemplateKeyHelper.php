<?php

namespace common\helpers;

use yii\helpers\StringHelper;

class EmailTemplateKeyHelper {

	public const SETTLEMENT_PAY_PAID = 'settlement' . self::PART_SEPARATOR . 'pay' . self::PART_SEPARATOR . 'paid';

	public const SETTLEMENT_CREATE = 'settlement' . self::PART_SEPARATOR . 'create';

	public const CUSTOMER = 'customer';
	public const WORKER = 'worker';

	private const ISSUE_TYPES_KEY = 'issueTypes:';
	private const IDS_SEPARATOR = ',';
	private const PART_SEPARATOR = '.';

	public static function generateKey(array $parts): string {
		return implode(static::PART_SEPARATOR, $parts);
	}

	public static function issueTypesKeyPart(array $ids): string {
		if (empty($ids)) {
			return '';
		}
		return static::ISSUE_TYPES_KEY . static::implodeIds($ids);
	}

	public static function isForIssueType(string $key, int $id): bool {
		$pos = strpos($key, static::ISSUE_TYPES_KEY);
		if ($pos === false) {
			return true;
		}
		$parts = static::explodeKey($key);
		foreach ($parts as $part) {
			if (StringHelper::startsWith($part, static::ISSUE_TYPES_KEY)) {
				$ids = static::explodeIds(str_replace(static::ISSUE_TYPES_KEY, '', $part));
				if (empty($ids) || in_array($id, $ids)) {
					return true;
				}
			}
		}
		return false;
	}

	private static function implodeIds(array $ids): string {
		return implode(static::IDS_SEPARATOR, $ids);
	}

	private static function explodeIds(string $string): array {
		return explode(static::IDS_SEPARATOR, $string);
	}

	private static function explodeKey(string $key): array {
		return explode(static::PART_SEPARATOR, $key);
	}
}
