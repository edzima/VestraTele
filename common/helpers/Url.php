<?php

namespace common\helpers;

use Yii;
use yii\helpers\BaseUrl;

/**
 * Base Url helper.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Url extends BaseUrl {

	public const ROUTE_ISSUE_INDEX = '/issue/issue/index';
	public const PARAM_ISSUE_PARENT_TYPE = 'parentTypeId';
	protected const ROUTE_ISSUE_VIEW = '/issue/issue/view';
	protected const ROUTE_SETTLEMENT_VIEW = '/settlement/calculation/view';

	protected const ROUTE_ISSUE_FILE_DOWNLOAD = '/file/issue/download';
	protected const ROUTE_LEAD_VIEW = '/lead/lead/view';

	public static function issuesIndexRoute(): string {
		if (YII_IS_FRONTEND) {
			return '/issue/index';
		}
		return '/issue/issue/index';
	}

	public static function issueViewRoute(): string {
		if (YII_IS_FRONTEND) {
			return '/issue/view';
		}
		return '/issue/issue/view';
	}

	public static function summonViewRoute(): string {
		if (YII_IS_FRONTEND) {
			return '/summon/view';
		}
		return '/issue/summon/view';
	}


	public static function issueFileDownload(int $issueId, int $fileId, $schema = false) {
		return static::to([
			static::ROUTE_ISSUE_FILE_DOWNLOAD,
			'issue_id' => $issueId,
			'file_id' => $fileId,
		], $schema);
	}

	public static function issueView(int $id, $schema = false): string {
		return static::toRoute([static::issueViewRoute(), 'id' => $id], $schema);
	}

	public static function summonView(int $id, $schema = false): string {
		return static::toRoute([static::summonViewRoute(), 'id' => $id], $schema);
	}

	public static function issuesParentType(int $id, array $params = []): string {
		$params[static::PARAM_ISSUE_PARENT_TYPE] = $id;
		array_unshift($params, static::ROUTE_ISSUE_INDEX);
		return static::to($params);
	}

	public static function leadView(int $id, $schema = false): string {
		return static::toRoute([static::ROUTE_LEAD_VIEW, 'id' => $id], $schema);
	}

	public static function settlementView(int $id, $schema = false): string {
		return static::toRoute([static::ROUTE_SETTLEMENT_VIEW, 'id' => $id], $schema);
	}

	static function getUrlManager() {
		if (!empty(static::managerConfig())) {
			static::$urlManager = Yii::createObject(static::managerConfig());
		}
		return parent::getUrlManager();
	}

	protected static function managerConfig(): array {
		return [];
	}

}
