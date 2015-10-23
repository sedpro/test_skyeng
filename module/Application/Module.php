<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'PupilTable' =>  function($sm) {
                    return $sm->get('tableFactory')->create($sm, 'Pupil', 'pupil');
                },
                'TeacherTable' =>  function($sm) {
                    return $sm->get('tableFactory')->create($sm, 'Teacher', 'teacher');
                },
                'TeacherPupilTable' =>  function($sm) {
                    return $sm->get('tableFactory')->create($sm, 'TeacherPupil', 'teacher_pupil');
                },
            ),
        );
    }

    public function getConsoleUsage() {
        return array(
            'install [--pupils=] [--teachers=]' => 'creates tables and fills them with random data',
        );
    }
}
