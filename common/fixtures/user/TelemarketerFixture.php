<?php

namespace common\fixtures\user;

use common\models\user\Worker;

class TelemarketerFixture extends WorkerFixture {

	public array $roles = [Worker::ROLE_TELEMARKETER];

}
