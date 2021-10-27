<?php

namespace common\components\message;

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
			if (MessageTemplateKeyHelper::isForIssueType($templateKey, $typeId)) {
				return $template;
			}
		}
		Yii::warning("Not found templates like key: $key for $language for Issue Type ID: $typeId.", 'messageTemplate');

		return null;
	}
}
