<?php

namespace common\components\message;

use common\models\message\IssueMessagesForm;
use common\modules\lead\models\LeadSmsForm;
use Yii;
use yii\data\ActiveDataProvider;
use ymaker\email\templates\components\TemplateManager;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\queries\EmailTemplateQuery;

class MessageTemplateManager extends TemplateManager implements KeyMessageTemplateManager, IssueMessageManager {

	/**
	 * {@inheritDoc}
	 */
	public function getTemplatesLikeKey(string $key, string &$language = null): ?array {
		$language = $language ?: Yii::$app->language;
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $this->repository->getDataProvider();
		$dataProvider->pagination = false;
		/** @var $query EmailTemplateQuery */
		$query = $dataProvider->query;
		$query->andWhere(['like', 'key', $key]);
		$query->withTranslation($language);
		$models = $dataProvider->getModels();
		if (empty($models)) {
			Yii::warning("Not found templates like key: '$key'.", 'messageTemplate');
			return null;
		}
		$templates = [];
		/** @var EmailTemplate[] $models */
		foreach ($models as $model) {
			$translation = $model->getTranslation($language);
			if ($translation && !$translation->isNewRecord) {
				$templates[$model->key] = MessageTemplate::buildFromEntity($translation);
			}
		}
		return $templates;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIssueTypeTemplatesLikeKey(string $key, int $typeId, string $language = null): ?MessageTemplate {
		$templates = $this->getTemplatesLikeKey($key, $language);
		if (empty($templates)) {
			return null;
		}
		foreach ($templates as $templateKey => $template) {
			if (IssueMessagesForm::isForIssueType($templateKey, $typeId)) {
				return $template;
			}
		}
		Yii::warning("Not found templates like key: $key for $language for Issue Type ID: $typeId.", __FUNCTION__);

		return null;
	}

	/**
	 * @param string $key
	 * @param int $typeId
	 * @param string|null $language
	 * @return MessageTemplate[]
	 */
	public function getLeadTypeTemplatesLikeKey(string $key, int $typeId, string $language = null): array {
		$templates = $this->getTemplatesLikeKey($key, $language);
		if (empty($templates)) {
			return [];
		}
		$typeTemplates = array_filter(
			$templates,
			function (MessageTemplate $messageTemplate, string $key) use ($typeId): bool {
				return LeadSmsForm::isForLeadType($key, $typeId);
			},
			ARRAY_FILTER_USE_BOTH
		);

		if (empty($typeTemplates)) {
			Yii::warning("Not found templates like key: $key for $language for Lead Type ID: $typeId.", __FUNCTION__);
		}
		return $typeTemplates;
	}
}
