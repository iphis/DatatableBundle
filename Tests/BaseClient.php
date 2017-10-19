<?php

namespace Iphis\DatatableBundle\Tests;

use Doctrine\ORM\EntityManager;
use Iphis\DatatableBundle\Tests\Functional\Entity\Feature;
use Iphis\DatatableBundle\Tests\Functional\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseClient extends WebTestCase
{
    /**
     * @param Client $client
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function buildDatabase($client)
    {
        /** @var EntityManager $em */
        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array(
            $em->getClassMetadata("\Iphis\DatatableBundle\Tests\Functional\Entity\Product"),
            $em->getClassMetadata("\Iphis\DatatableBundle\Tests\Functional\Entity\Feature"),
        );
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        $this->insertData($em);
    }

    /**
     * @param EntityManager $em
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function insertData($em)
    {
        $p = new Product;
        $p->setName('Laptop')
            ->setPrice(1000)
            ->setDescription('New laptop');
        $em->persist($p);

        $p = new Product;
        $p->setName('Desktop')
            ->setPrice(5000)
            ->setDescription('New Desktop');
        $em->persist($p);

        $f = new Feature;
        $f->setName('CPU I7 Generation')
            ->setProduct($p);

        $f1 = new Feature;
        $f1->setName('SolidState drive')
            ->setProduct($p);

        $f2 = new Feature;
        $f2->setName('SLI graphic card ')
            ->setProduct($p);

        $em->persist($f);
        $em->persist($f1);
        $em->persist($f2);

        $em->flush();
    }
}
