<?php

namespace Iphis\DatatableBundle\Tests\src\DatatableTest;

use Iphis\DatatableBundle\Tests\BaseClient;
use Iphis\DatatableBundle\Util\Datatable;

/**
 * Description of DatatableStaticTest
 *
 * @group DatatableStaticTest
 *
 * @author waldo
 */
class DatatableStaticTest extends BaseClient
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No instance found for datatable, you should set a datatable id in your action with "setDatatableId" using the id from your view
     * @throws \Exception
     */
    public function test_getInstanceWithoutInstance()
    {
        $i = Datatable::getInstance("fakes");
    }
}
