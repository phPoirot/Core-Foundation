<?php
namespace Module\Foundation\Actions\Helper;

use Module\Foundation\Actions\aAction;
use Poirot\Application\aSapi;
use Poirot\Std\Struct\DataEntity;


class ConfigAction 
    extends aAction
{
    /** @var  DataEntity */
    protected $config;


    /**
     * Invoke Config
     *
     * @param null $confKey
     * @param null $default
     *
     * @return mixed|DataEntity
     */
    function __invoke($confKey = null, $default = null)
    {
        $config = $this->_attainSapiConfig();
        if ($confKey !== null)
            $config = $config->get($confKey, $default);

        return $config;
    }

    
    // ..
    
    protected function _attainSapiConfig()
    {
        if (! $this->config ) {
            /** @var aSapi $sapi */
            $services = $this->services; 
            $sapi     = $services->get('/sapi');
            $this->config = $sapi->config();
        }

        return $this->config;
    }
}
