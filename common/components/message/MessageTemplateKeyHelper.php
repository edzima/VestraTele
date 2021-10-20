<?php

namespace common\components\message;

use yii\helpers\StringHelper;

class MessageTemplateKeyHelper {

	public const TYPE_SMS = 'sms';
	public const TYPE_EMAIL = 'email';

	protected const ISSUE_TYPES_KEY = 'issueTypes:';
	protected const IDS_SEPARATOR = ',';
	protected const PART_SEPARATOR = '.';

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
