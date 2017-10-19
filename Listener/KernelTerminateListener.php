<?php

namespace Iphis\DatatableBundle\Listener;

use Iphis\DatatableBundle\Util\Datatable;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class KernelTerminateListener
{
    public function onKernelTerminate()
    {
        Datatable::clearInstance();
    }
}
