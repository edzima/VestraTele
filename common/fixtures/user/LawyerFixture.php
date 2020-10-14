<?php

namespace common\fixtures\user;

use common\models\user\Worker;

class LawyerFixture extends WorkerFixture {

	public array $roles = [Worker::ROLE_LAWYER];

}
