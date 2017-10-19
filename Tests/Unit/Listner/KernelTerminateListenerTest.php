<?php

namespace Iphis\DatatableBundle\Tests\Unit\Listner;

use Iphis\DatatableBundle\Listener\KernelTerminateListener;
use PHPUnit\Framework\TestCase;

/**
 * @group KernelTerminateListenerTest
 */
class KernelTerminateListenerTest extends TestCase
{
    public function testOnKernelTerminate()
    {

        $dt = new \Iphis\DatatableBundle\Util\Datatable(
            $this->getMockBuilder("Doctrine\ORM\EntityManager")->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder("Symfony\Component\HttpFoundation\RequestStack")->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder("Iphis\DatatableBundle\Util\Factory\Query\DoctrineBuilder")->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder("Iphis\DatatableBundle\Util\Formatter\Renderer")->disableOriginalConstructor()->getMock(),
            array("js" => array())
        );

        $dt->setDatatableId("testOnKernelTerminate");

        $listner = new KernelTerminateListener();

        $listner->onKernelTerminate();

        $this->assertFalse($dt->hasInstanceId("testOnKernelTerminate"));
    }
}
