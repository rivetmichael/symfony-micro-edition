<?php

namespace MichaelR\SfMicro\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use MichaelR\SfMicro\MicroKernel;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Response as HttpReponse;

class MicroKernelTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return MicroKernel::class;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $options
     * @param array $server
     * @return Client
     */
    public static function createClient(array $options = [], array $server = [])
    {
        static::bootKernel($options);

        $client = new Client(static::$kernel);
        $client->setServerParameters($server);

        return $client;
    }

    public function test_homepage_works()
    {
        $client = self::createClient();
        $client->request('GET', '/');

        $this->assertEquals(HttpReponse::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function test_profiler_works()
    {
        $client = self::createClient(['environment' => 'dev']);
        $client->request('GET', '/_profiler/latest');

        $this->assertEquals(HttpReponse::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function test_profiler_dont_work_in_prod()
    {
        $client = self::createClient(['environment' => 'prod']);
        $client->request('GET', '/_profiler/latest');

        $this->assertEquals(HttpReponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function test_non_existing_page()
    {
        $client = self::createClient();
        $client->request('GET', '/lol');

        $this->assertEquals(HttpReponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
