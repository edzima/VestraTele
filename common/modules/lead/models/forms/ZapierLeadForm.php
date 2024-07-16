<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\entities\FBCampaign;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;

class ZapierLeadForm extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;
	public $provider = Lead::PROVIDER_FORM_ZAPIER;

	public ?string $fb_campaign_id = null;
	public ?string $fb_campaign_name = null;
	public ?string $fb_adset_id = null;
	public ?string $fb_adset_name = null;
	public ?string $fb_ad_id = null;
	public ?string $fb_ad_name = null;

	public function rules(): array {
		return array_merge([
			[
				[
					'fb_campaign_id', 'fb_campaign_name',
					'fb_adset_id', 'fb_adset_name',
					'fb_ad_id', 'fb_ad_name',
				],
				'string',
			],
		], parent::rules());
	}

	public function afterValidate(): void {
		parent::afterValidate();
		if (empty($this->campaign_id)) {
			$this->setPixelCampaign();
		}
	}

	protected function setPixelCampaign(): void {
		if (!empty($this->fb_ad_id)) {
			$pixelCampaign = new FBCampaign();
			$pixelCampaign->createCampaigns = true;
			$pixelCampaign->setAttributes([
				'campaignId' => (string) $this->fb_campaign_id,
				'campaignName' => (string) $this->fb_campaign_name,
				'adsetId' => (string) $this->fb_adset_id,
				'adsetName' => (string) $this->fb_adset_name,
				'adId' => (string) $this->fb_ad_id,
				'adName' => (string) $this->fb_ad_name,
			]);
			$campaign = $pixelCampaign->getLeadCampaign();
			if ($campaign) {
				$this->campaign_id = $campaign->id;
			}
		}
	}

}
