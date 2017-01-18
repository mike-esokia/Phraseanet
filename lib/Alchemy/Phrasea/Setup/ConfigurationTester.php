<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Setup;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Phrasea\Core\Configuration\ConfigurationInterface;
use Alchemy\Phrasea\Core\Version as PhraseaVersion;
use Alchemy\Phrasea\Core\Version\VersionRepository;
use Alchemy\Phrasea\Databox\DataboxRepository;
use Alchemy\Phrasea\Setup\Version\Migration\MigrationInterface;
use Alchemy\Phrasea\Setup\Version\Probe\Probe31;
use Alchemy\Phrasea\Setup\Version\Probe\Probe35;
use Alchemy\Phrasea\Setup\Version\Probe\Probe38;
use Alchemy\Phrasea\Setup\Version\Probe\ProbeInterface as VersionProbeInterface;
use Alchemy\Phrasea\Setup\Probe\BinariesProbe;
use Alchemy\Phrasea\Setup\Probe\CacheServerProbe;
use Alchemy\Phrasea\Setup\Probe\DataboxStructureProbe;
use Alchemy\Phrasea\Setup\Probe\FilesystemProbe;
use Alchemy\Phrasea\Setup\Probe\LocalesProbe;
use Alchemy\Phrasea\Setup\Probe\PhpProbe;
use Alchemy\Phrasea\Setup\Probe\SearchEngineProbe;
use Alchemy\Phrasea\Setup\Probe\SubdefsPathsProbe;
use Alchemy\Phrasea\Setup\Probe\SystemProbe;
use Alchemy\Phrasea\Setup\Version\Probe\VersionProbesFactory;
use vierbergenlars\SemVer\version;

class ConfigurationTester
{

    /**
     * @var VersionRepository
     */
    private $appboxVersionRepository;

    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @var DataboxRepository
     */
    private $databoxRepository;

    /**
     * @var PhraseaVersion
     */
    private $sourceVersion;

    /**
     * @var VersionProbeInterface[]
     */
    private $versionProbes;

    /**
     * @var VersionProbesFactory
     */
    private $versionProbesFactory;

    /**
     * @param VersionRepository $appboxVersionRepository
     * @param ConfigurationInterface $config
     * @param DataboxRepository $databoxRepository
     * @param VersionProbesFactory $versionProbesFactory
     */
    public function __construct(
        VersionRepository $appboxVersionRepository,
        ConfigurationInterface $config,
        DataboxRepository $databoxRepository,
        VersionProbesFactory $versionProbesFactory
    ) {
        $this->appboxVersionRepository = $appboxVersionRepository;
        $this->config = $config;
        $this->databoxRepository = $databoxRepository;
        $this->versionProbesFactory = $versionProbesFactory;
        $this->sourceVersion = new PhraseaVersion();
    }

    /**
     * @param BaseApplication $app
     * @return Requirements\RequirementInterface[]
     */
    public function getRequirements(BaseApplication $app)
    {
        return [
            BinariesProbe::create($app),
            CacheServerProbe::create($app),
            DataboxStructureProbe::create($app),
            FilesystemProbe::create($app),
            LocalesProbe::create($app),
            PhpProbe::create($app),
            SearchEngineProbe::create($app),
            SubdefsPathsProbe::create($app),
            SystemProbe::create($app),
        ];
    }

    /**
     * @return Version\Migration\MigrationInterface[]
     */
    public function getMigrations()
    {
        $migrations = [];

        if ($this->isUpToDate()) {
            return $migrations;
        }

        foreach ($this->getVersionProbes() as $probe) {
            if ($probe->isMigrable()) {
                $migrations[] = $probe->getMigration();
            }
        }

        return $migrations;
    }

    public function getVersionProbes()
    {
        return $this->versionProbes ?: $this->versionProbes = $this->versionProbesFactory->createProbes();
    }

    /**
     * @param VersionProbeInterface $probe
     */
    public function registerVersionProbe(VersionProbeInterface $probe)
    {
        $this->versionProbesFactory->registerVersionProbe($probe);
    }

    /**
     * Return true if got the latest configuration file.
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->config->isSetup();
    }

    /**
     * @return bool
     */
    public function isUpToDate()
    {
        return $this->isInstalled() && !$this->isUpgradable();
    }

    /**
     * @return bool
     */
    public function isBlank()
    {
        return !$this->isInstalled() && !$this->isMigrable();
    }

    /**
     *
     * @return bool
     */
    public function isUpgradable()
    {
        if (!$this->isInstalled()) {
            return false;
        }

        $appboxVersion = $this->appboxVersionRepository->getVersion();
        $upgradable = version::lt($appboxVersion, $this->sourceVersion);

        if (! $upgradable) {
            foreach ($this->databoxRepository->findAll() as $databox) {
                if (version::lt($databox->get_version(), $this->sourceVersion->getNumber())) {
                    $upgradable = true;
                    break;
                }
            }
        }

        return $upgradable;
    }

    /**
     * Returns true if a major migration script can be executed
     *
     * @return bool
     */
    public function isMigrable()
    {
        return (Boolean) $this->getMigrations();
    }
}
