<?php

namespace Alchemy\Phrasea\Setup\Probe;

use Alchemy\Phrasea\Setup\RequirementCollectionInterface;

interface ProbeInterfaceFactory
{
    /***
     * @return RequirementCollectionInterface
     */
    public function createRequirementProbe();
}
