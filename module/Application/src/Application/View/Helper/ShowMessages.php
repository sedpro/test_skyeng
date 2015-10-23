<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\FlashMessenger;

/**
 * Class ShowMessages
 *
 * renders FlashMessages in a twitter bootstrap compatible way
 *
 */
class ShowMessages extends AbstractHelper
{
    public function __invoke()
    {
        $messenger = new FlashMessenger();
        $namespaces = ['default', 'success', 'error', 'warning', 'info'];

        $result = '';
        foreach($namespaces as $namespace) {
            $messenger->setNamespace($namespace);
            if ($namespace === 'default') {
                $namespace = 'info';
            }
            if ($namespace === 'error') {
                $namespace = 'danger';
            }
            foreach ($messenger->getMessages() as $message) {
                $result .= $this->getView()->partial('partial/message', compact('message', 'namespace'));
            }
        }

        return $result;
    }
}