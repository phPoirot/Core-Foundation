<?php
namespace Module\Foundation\Services;

use Module\Foundation\Services\PathService\PathAction;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;

/*
 Merged Config:

\Module\Foundation\Services\PathService::CONF => [
    'paths' => [
        // According to route name 'www-assets' to serve statics files
        // @see cor-http_foundation.routes
        'tenderbin-media_cdn' => function($args) {
            $uri = $this->assemble('$serverUrlTenderBin', $args);
            return $uri;
        },
    ],
    'variables' => [
        'serverUrlTenderBin' => function() {
            return \Module\HttpFoundation\Actions::url(
                'main/tenderbin/resource/get'
                , [ 'resource_hash' => '$hash' ]
                , \Module\HttpFoundation\Actions\Url::INSTRUCT_NOTHING
            );
        },
    ],
],


 Access:

 \Module\Foundation\Actions::Path()
    ->assemble('$baseUrl');

*/

class PathService
    extends aServiceContainer
{
    const CONF = 'path';
         
    /**
     * @var string Service Name
     */
    protected $name = 'path';


    /**
     * Create Service
     *
     * @return PathAction|callable
     * @throws \Exception
     */
    function newService()
    {
        $pathAction = new PathAction;

        return $this->_buildFromMergedConf($pathAction);
    }


    // ..

    /**
     * Build with merged config
     *
     * @param PathAction $pathAction
     *
     * @return PathAction
     * @throws \Exception
     */
    private function _buildFromMergedConf(PathAction $pathAction)
    {
        /** @var DataEntity $config */
        $services = $this->services();
        $config   = $services->get('/sapi')->config();
        $config   = $config->{\Module\Foundation\Module::class}->path;

        $pathAction->with( $pathAction::parseWith($config) );
        return $pathAction;
    }
}
