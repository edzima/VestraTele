<?php

namespace common\components\message;

use yii\helpers\StringHelper;

class MessageTemplateKeyHelper {

	public const TYPE_SMS = 'sms';
	public const TYPE_EMAIL = 'email';

	private const KEY_ISSUE_TYPES = 'issueTypes';

	protected const PART_SEPARATOR = '.';
	protected const VALUES_PREFIX = ':';
	protected const VALUES_SEPARATOR = ',';

	public static function generateKey(array $parts, string $separator = self::PART_SEPARATOR): string {
		$keys = [];
		foreach ($parts as $key => $value) {
			if (is_array($value)) {
				$valuesSeparator = is_string($key) ? static::VALUES_SEPARATOR : $separator;
				$value = self::generateKey($value, $valuesSeparator);
			}
			if (!empty($value)) {
				if (is_string($key)) {
					$keys[] = $key . static::VALUES_PREFIX . $value;
				} else {
					$keys[] = $value;
				}
			}
		}
		return implode($separator, $keys);
	}

	public static function issueTypesKeyPart(array $ids): string {
		if (empty($ids)) {
			return '';
		}
		return static::generateKey([static::KEY_ISSUE_TYPES => $ids], static::VALUES_SEPARATOR);
	}

	public static function isForIssueType(string $key, int $id): bool {
		$partKey = static::KEY_ISSUE_TYPES . static::VALUES_PREFIX;
		if (strpos($key, $partKey) === false) {
			return true;
		}
		$parts = static::explodeKey($key);
		foreach ($parts as $part) {
			if (StringHelper::startsWith($part, $partKey)) {
				$withoutKey = str_replace($partKey, '', $part);
				$ids = static::explodeValues($withoutKey);
				if (empty($ids) || in_array($id, $ids)) {
					return true;
				}
			}
		}
		return false;
	}

	private static function implodeValues(array $values): string {
		return implode(static::VALUES_SEPARATOR, $values);
	}

	private static function explodeValues(string $string): array {
		return explode(static::VALUES_SEPARATOR, $string);
	}

	private static function explodeKey(string $key): array {
		return explode(static::PART_SEPARATOR, $key);
	}
}
