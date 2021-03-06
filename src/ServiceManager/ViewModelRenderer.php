<?php
namespace Module\Foundation\ServiceManager;

use Poirot\Ioc\Container;
use Poirot\Ioc\Interfaces\iContainer;
use Poirot\Ioc\Interfaces\Respec\iServicesAware;
use Poirot\Ioc\Interfaces\Respec\iServicesProvider;
use Poirot\Std\Struct\DataEntity;
use Poirot\View\ViewModel\RendererPhp;


/**
 * @method DataEntity                                           config($key = null)
 * @method \Module\Foundation\Actions\Helper\ViewAction         view($template = null, $variables = null)
 * @method \Module\Foundation\Services\PathService\PathAction   path($arg = null)
 * @method \Module\Foundation\Actions\Helper\CycleAction        cycle($action = null, $steps = 1, $reset = true)
 */
class ViewModelRenderer
    extends RendererPhp
    // services injected and accessible
    implements iServicesAware
    , iServicesProvider
{
    /** @var Container */
    protected $sc;
    /** @var string last module action container to invoke actions from */
    protected $lastActionContainer = 'Foundation';


    /**
     * Proxy Call to default Action`s container
     *
     * exp. $this->action('application')->url(..)
     *
     * @param $method
     * @param array $args
     *
     * @return mixed
     * @throws \Exception
     */
    function __call($method, array $args)
    {
        if ($this->hasMethod($method))
            return parent::__call($method, $args);

        if (false === $foundationActions = $this->services()->from('/module/'.$this->lastActionContainer.'/actions'))
            throw new \Exception(sprintf(
                'Nested Module Action Container (%s) not exists; While Retrieve ::%s method.'
                , $this->lastActionContainer, $method
            ));

        if ($foundationActions->has($method))
            $method = $foundationActions->get($method);


        return call_user_func_array($method, $args);
    }

    /**
     * Proxy Call to Action`s container
     * exp. $this->action('Foundation')->url(..)
     *
     * @param string $module
     *
     * @return mixed
     */
    function action($module = 'Foundation')
    {
        $this->lastActionContainer = $module;
        return $this;
    }

    
    // Implement Services Aware:

    /**
     * Set Service Container
     *
     * @param iContainer $container
     *
     * @return $this
     */
    function setServices(iContainer $container)
    {
        $this->sc = $container;
        return $this;
    }

    /**
     * Services Container
     *
     * @return Container
     */
    function services()
    {
        return $this->sc;
    }


    // ..

    protected function __alertError()
    {
        $errfile = "unknown file";
        $errstr  = "shutdown";
        $errno   = E_CORE_ERROR;
        $errline = 0;
        $error = error_get_last();
        if( $error !== NULL) {
            $errno   = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr  = $error["message"];
            $message = "( ! )  ". $errstr.' '.$errfile.' line:'.$errline;
            $message = implode(" '+ '", explode("\n", addslashes($message)));
            echo "<script type=\"text/javascript\">alert('{$message}')</script>";
        }
    }

}
