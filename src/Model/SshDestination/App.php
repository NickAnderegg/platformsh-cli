<?php

namespace Platformsh\Cli\Model\SshDestination;

use Platformsh\Cli\Model\AppConfig;
use Platformsh\Client\Model\Deployment\WebApp;
use Platformsh\Client\Model\Environment;

class App implements SshDestinationInterface
{
    private $webApp;
    private $environment;

    /**
     * @param \Platformsh\Client\Model\Deployment\WebApp $webApp
     * @param \Platformsh\Client\Model\Environment       $environment
     */
    public function __construct(WebApp $webApp, Environment $environment) {
        $this->webApp = $webApp;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getSshUrl()
    {
        return $this->environment->getSshUrl($this->webApp->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->webApp->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'app';
    }

    /**
     * {@inheritdoc}
     */
    public function getMounts() {
        $config = AppConfig::fromWebApp($this->webApp)->getNormalized();

        return !empty($config['mounts']) ? $config['mounts'] : [];
    }
}
