<?php

namespace common\components\message;

interface KeyMessageTemplateManager {

	/**
	 * @param string $key
	 * @param string|null $language
	 * @return MessageTemplate[]|null indexed by Key
	 */
	public function getTemplatesLikeKey(string $key, string &$language = null): ?array;
}
