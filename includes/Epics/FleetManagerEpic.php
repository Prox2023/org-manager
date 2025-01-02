<?php

namespace OrgManager\Epics;

use OrgManager\Features\Epic;
use OrgManager\Features\Fleet\FleetOverviewFeature;

class FleetManagerEpic extends Epic {
    public function __construct() {
        parent::__construct(
            'fleet-manager',
            'Fleet Manager',
            'fleet-manager'
        );

        // Add your features here
        $this->add_feature(new FleetOverviewFeature());
    }
}
