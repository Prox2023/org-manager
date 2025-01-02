<?php

namespace OrgManager\Epics;

use OrgManager\Features\DiscordAuthFeature;
use OrgManager\Features\Epic;

class AdministrationEpic extends Epic {
    public function __construct() {
        parent::__construct(
            'administration',
            'Administration',
            'admin'
        );

        $this->add_feature(new DiscordAuthFeature());
    }
} 