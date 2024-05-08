<?php

use common\modules\lead\models\LeadUser;
use console\base\Migration;

/**
 * Class m240506_105550_leads_user_activity
 *
 */
class m240506_105550_leads_user_activity extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->addColumn('{{%lead_user}}', 'first_view_at', $this->timestamp()->null());
		$this->addColumn('{{%lead_user}}', 'action_at', $this->timestamp()->null());
		$this->addColumn('{{%lead_user}}', 'last_view_at', $this->timestamp()->null());

		foreach (LeadUser::find()
			->with('lead')
			->with('lead.reports')
			->batch() as $rows) {
			foreach ($rows as $model) {
				/** @var LeadUser $model */
				$dates = [];
				foreach ($model->lead->reports as $report) {
					if ($report->owner_id === $model->user_id) {
						$dates[] = $report->created_at;
					}
				}
				if (!empty($dates)) {
					$min = min($dates);
					$max = max($dates);
					$model->updateAttributes([
						'first_view_at' => $min,
						'last_view_at' => $max,
						'action_at' => $max,
					]);
				}
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropColumn('{{%lead_user}}', 'first_view_at');
		$this->dropColumn('{{%lead_user}}', 'action_at');
		$this->dropColumn('{{%lead_user}}', 'last_view_at');
	}
}
