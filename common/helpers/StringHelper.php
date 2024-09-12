<?php

namespace common\helpers;

use yii\helpers\BaseStringHelper;

class StringHelper extends BaseStringHelper {

	protected static function w1250Map(): array {
		return [
			chr(0x8A) => chr(0xA9),
			chr(0x8C) => chr(0xA6),
			chr(0x8D) => chr(0xAB),
			chr(0x8E) => chr(0xAE),
			chr(0x8F) => chr(0xAC),
			chr(0x9C) => chr(0xB6),
			chr(0x9D) => chr(0xBB),
			chr(0xA1) => chr(0xB7),
			chr(0xA5) => chr(0xA1),
			chr(0xBC) => chr(0xA5),
			chr(0x9F) => chr(0xBC),
			chr(0xB9) => chr(0xB1),
			chr(0x9A) => chr(0xB9),
			chr(0xBE) => chr(0xB5),
			chr(0x9E) => chr(0xBE),
			chr(0x80) => '&euro;',
			chr(0x82) => '&sbquo;',
			chr(0x84) => '&bdquo;',
			chr(0x85) => '&hellip;',
			chr(0x86) => '&dagger;',
			chr(0x87) => '&Dagger;',
			chr(0x89) => '&permil;',
			chr(0x8B) => '&lsaquo;',
			chr(0x91) => '&lsquo;',
			chr(0x92) => '&rsquo;',
			chr(0x93) => '&ldquo;',
			chr(0x94) => '&rdquo;',
			chr(0x95) => '&bull;',
			chr(0x96) => '&ndash;',
			chr(0x97) => '&mdash;',
			chr(0x99) => '&trade;',
			chr(0x9B) => '&rsquo;',
			chr(0xA6) => '&brvbar;',
			chr(0xA9) => '&copy;',
			chr(0xAB) => '&laquo;',
			chr(0xAE) => '&reg;',
			chr(0xB1) => '&plusmn;',
			chr(0xB5) => '&micro;',
			chr(0xB6) => '&para;',
			chr(0xB7) => '&middot;',
			chr(0xBB) => '&raquo;',
		];
	}

	public static function between(string $string, string $start, string $end): ?string {
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini === false) {
			return null;
		}
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		$between = substr($string, $ini, $len);
		return $between ?: null;
	}

	public static function encodeToUtf8(string $text): string {
		return html_entity_decode(mb_convert_encoding(strtr($text, static::w1250Map()), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
	}

	public static function shortName(string $fullName, int $secondLength = 1): string {
		$names = explode(' ', $fullName);
		$shortName = $names[0];
		if (isset($names[1])) {
			$shortName .= ' ' . mb_substr($names[1], 0, $secondLength) . '.';
		}
		return $shortName;
	}

}
