<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Plugin\Management;

use Alchemy\Phrasea\Plugin\Exception\RegistrationFailureException;
use Alchemy\Phrasea\Plugin\Schema\Manifest;

class AutoloaderGenerator
{
    private $pluginDirectory;

    public function __construct($pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }

    public function write($manifests)
    {
        $this
            ->doWrite('autoload.php', $this->createLoader($manifests))
            ->doWrite('services.php', $this->createServices($manifests))
            ->doWrite('commands.php', $this->createCommands($manifests))
            ->doWrite('twig-paths.php', $this->createTwigPathsMap($manifests))
            ->doWrite('login.less', $this->createLoginLess($manifests))
            ->doWrite('account.less', $this->createAccountLess($manifests));

        return $this;
    }

    /**
     * @param Manifest[] $manifests
     * @return string
     */
    private function createLoginLess($manifests)
    {
        $buffer = <<<EOF
// This file is automatically generated, please do not edit it.
EOF;

        foreach ($manifests as $manifest) {
            $filepath = $this->pluginDirectory . DIRECTORY_SEPARATOR . $manifest->getName() . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR . 'login.less';
            if (is_file($filepath)) {
                $relativePath = $manifest->getName() . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR . 'login.less';
                $buffer .= <<<EOF

    @import "$relativePath";
EOF;
            }
        }

        return $buffer;
    }

    /**
     * @param Manifest[] $manifests
     * @return string
     */
    private function createAccountLess($manifests)
    {
        $buffer = <<<EOF
// This file is automatically generated, please do not edit it.
EOF;

        foreach ($manifests as $manifest) {
            $filepath = $this->pluginDirectory . DIRECTORY_SEPARATOR . $manifest->getName() . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR . 'account.less';
            if (is_file($filepath)) {
                $relativePath = $manifest->getName() . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR . 'account.less';
                $buffer .= <<<EOF

    @import "$relativePath";
EOF;
            }
        }

        return $buffer;
    }

    private function doWrite($file, $data)
    {
        if (false === file_put_contents($this->pluginDirectory . DIRECTORY_SEPARATOR . $file, $data)) {
            throw new RegistrationFailureException(sprintf('Failed to write %s', $file));
        }

        return $this;
    }

    /**
     * @param Manifest[] $manifests
     * @return string
     */
    private function createLoader($manifests)
    {
        $buffer = <<<EOF
<?php

// This file is automatically generated, please do not edit it.
// To update configuration, use bin/console plugins:* commands.

return call_user_func(function () {
EOF;

        foreach ($manifests as $manifest) {
            $autoloader = '/' . $manifest->getName() . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
            $buffer .= <<<EOF

    require __DIR__ . '$autoloader';
EOF;
        }

        // composer loader are preprent
        $autoloader = '/../vendor/autoload.php';
        $buffer .= <<<EOF

    \$loader = require __DIR__ . '$autoloader';

    return \$loader;\n});

EOF;

        return $buffer;
    }

    /**
     * @param Manifest[] $manifests
     * @return string
     */
    private function createServices($manifests)
    {
        $buffer = <<<EOF
<?php

// This file is automatically generated, please do not edit it.
// To update configuration, use bin/console plugins:* commands.

use Alchemy\Phrasea\Application;

return call_user_func(function (Application \$app) {
    \$textdomains = \$app['plugin.locale.textdomains'];

EOF;

        foreach ($manifests as $manifest) {
            $localePath = $this->getPluginBaseDir($manifest) . DIRECTORY_SEPARATOR . 'locale';
            $textdomain = 'plugin-' . $manifest->getName();

            if (is_dir($this->pluginDirectory . $localePath)) {
                $quotedName = $this->quote($manifest->getName());
                $quotedPath = $this->quote($localePath);
                $buffer .= <<<EOF

    // Plugin $quotedName
    \$textdomains['$textdomain'] = __DIR__ . $quotedPath;

EOF;
            }

            foreach ($manifest->getServices() as $service) {
                $class = $service['class'];
                $buffer .= <<<EOF
    \$app->register($class::create(\$app));

EOF;
            }
        }

        $buffer .= <<<EOF

    return \$app;
}, \$app);

EOF;

        return $buffer;
    }

    /**
     * @param Manifest[] $manifests
     * @return string
     */
    private function createCommands($manifests)
    {
        $buffer = <<<EOF
<?php

// This file is automatically generated, please do not edit it.
// To update configuration, use bin/console plugins:* commands.

use Alchemy\Phrasea\CLI;

return call_user_func(function (CLI \$cli) {

EOF;

        foreach ($manifests as $manifest) {
            foreach ($manifest->getCommands() as $command) {
                $class = $command['class'];
                $buffer .= <<<EOF
    \$cli->command($class::create());

EOF;
            }
        }

        $buffer .= <<<EOF

    return \$cli;
}, \$cli);

EOF;

        return $buffer;
    }

    /**
     * @param Manifest[] $manifests
     * @return string
     */
    public function createTwigPathsMap($manifests)
    {
        $buffer = <<<EOF
<?php

// This file is automatically generated, please do not edit it.
// To update configuration, use bin/console plugins:* commands.

return array(

EOF;

        foreach ($manifests as $manifest) {
            $namespace = $this->quote('plugin-' . $manifest->getName());
            $path = DIRECTORY_SEPARATOR . $manifest->getName() . DIRECTORY_SEPARATOR . 'views';

            if (is_dir($this->pluginDirectory . $path)) {
                $path = $this->quote($path);
                $buffer .= <<<EOF
    $namespace => __DIR__ . $path,

EOF;
            }

            foreach ($manifest->getTwigPaths() as $path) {
                $path = $this->quote(DIRECTORY_SEPARATOR . $manifest->getName() . DIRECTORY_SEPARATOR . $path);
                $buffer .= <<<EOF
    __DIR__ . $path,

EOF;
            }
        }

        $buffer .= <<<EOF
);

EOF;

        return $buffer;
    }

    private function quote($string)
    {
        return "'".str_replace("'", "\\'", $string)."'";
    }

    /**
     * @param Manifest $manifest
     * @return string
     */
    private function getPluginBaseDir(Manifest $manifest)
    {
        return DIRECTORY_SEPARATOR . $manifest->getName();
    }
}
