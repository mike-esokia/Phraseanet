<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Command\Setup;

use Alchemy\Phrasea\Command\AbstractCheckCommand;
use Alchemy\Phrasea\Setup\Requirements\BinariesRequirements;
use Alchemy\Phrasea\Setup\Requirements\CacheServerRequirement;
use Alchemy\Phrasea\Setup\Requirements\FilesystemRequirements;
use Alchemy\Phrasea\Setup\Requirements\LocalesRequirements;
use Alchemy\Phrasea\Setup\Requirements\PhpRequirements;
use Alchemy\Phrasea\Setup\Requirements\SystemRequirements;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckEnvironment extends AbstractCheckCommand
{
    const CHECK_OK = 0;
    const CHECK_WARNING = 1;
    const CHECK_ERROR = 2;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $description = "Performs a check against the environment";

        if ($name !== 'system:check') {
            $description = '[OBSOLETE, use system:check instead] ' . $description;
        }

        $this->setDescription($description);

        return $this;
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getName()!== 'system:check') {
            $output->writeln('This command has been replaced by "system:check" and will be removed in future versions.');
            $output->writeln('');
        }

        parent::doExecute($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function provideRequirements()
    {
        return [
            new BinariesRequirements(),
            new CacheServerRequirement(),
            new FilesystemRequirements(),
            new LocalesRequirements(),
            new PhpRequirements(),
            new SystemRequirements(),
        ];
    }
}
