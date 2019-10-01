<?php
namespace Archive;

use Archive\src\Model\Archive;
use Archive\src\Model\ArchiveTable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(){
        return [
            'factories' => [
                Model\ArchiveTable::class => function($container){
                    $tableGateway = $container->get(Model\ArchiveTableGateway::class);
                    return new Model\ArchiveTable($tableGateway);
                },
                Model\ArchiveTableGateway::class => function($container){
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Archive());
                    return new TableGateway('archive', $dbAdapter, null, $resultSetPrototype);
                }
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\ArchiveController::class => function($container) {
                    return new Controller\ArchiveController(
                        $container->get(Model\ArchiveTable::class)
                    );
                },
            ],
        ];
    }

    /**
     * @param  MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Register a "render" event, at high priority (so it executes prior
        // to the view attempting to render)
        $app = $e->getApplication();
        $app->getEventManager()->attach('render', [$this, 'registerJsonStrategy'], 100);
        $eventManager = $app->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'catchError']);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'catchError']);
    }

    /**
     * @param  MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function registerJsonStrategy(MvcEvent $e)
    {
        $app          = $e->getTarget();
        $locator      = $app->getServiceManager();
        $view         = $locator->get('Zend\View\View');
        $jsonStrategy = $locator->get('ViewJsonStrategy');

        // Attach strategy, which is a listener aggregate, at high priority
        $jsonStrategy->attach($view->getEventManager(), 100);
    }

    public function catchError(MvcEvent $event) {
        $exception = $event->getParam('exception');
        echo "Exception: ".$exception;
    }
}
