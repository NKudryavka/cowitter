<?php

namespace mpyw\TestOfCowitter;

require __DIR__ . '/../../assets/dummy_curl.php';

use mpyw\Co\Co;
use mpyw\Co\CURLException;
use mpyw\Cowitter\Client;
use mpyw\Cowitter\HttpException;
use mpyw\Cowitter\ResponseInterface;

use mpyw\Privator\Proxy;
use mpyw\Privator\ProxyException;

/**
 * @requires PHP 7.0
 */
class APIRestTest extends \Codeception\TestCase\Test {

    use \Codeception\Specify;
    private static $CredentialNormalizer;

    private static function t($v)
    {
        return json_decode(json_encode($v));
    }

    public function _before()
    {
        $this->c = new Client(['ck', 'cs', 't', 'ts'], [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
    }

    public function testHomeTimeline()
    {
        $count = mt_rand(1, 10);
        $this->assertEquals($count, count(
            $this->c->get('statuses/home_timeline', compact('count'))
        ));
    }

    public function testHomeTimelineAsync()
    {
        $count = mt_rand(1, 10);
        $this->assertEquals($count, count(
            Co::wait($this->c->getAsync('statuses/home_timeline', compact('count')))
        ));
    }

    public function testUpdate()
    {
        $status = bin2hex(openssl_random_pseudo_bytes(32));
        $this->assertEquals(
            $status,
            $this->c->post('statuses/update', compact('status'))->text
        );
    }

    public function testUpdateAsync()
    {
        $status = bin2hex(openssl_random_pseudo_bytes(32));
        $this->assertEquals(
            $status,
            Co::wait($this->c->postAsync('statuses/update', compact('status')))->text
        );
    }

    public function testUpdateWithMedia()
    {
        $status = bin2hex(openssl_random_pseudo_bytes(32));
        $this->assertEquals(
            (object)[
                'status' => 'test',
                'media[]' => sha1_file(__FILE__),
            ],
            $this->c->postMultipart('statuses/update_with_media', [
                'status' => 'test',
                'media[]' => new \CURLFile(__FILE__),
            ])
        );
    }

    public function testUpdateWithMediaAsync()
    {
        $status = bin2hex(openssl_random_pseudo_bytes(32));
        $this->assertEquals(
            (object)[
                'status' => 'test',
                'media[]' => sha1_file(__FILE__),
            ],
            Co::wait($this->c->postMultipartAsync('statuses/update_with_media', [
                'status' => 'test',
                'media[]' => new \CURLFile(__FILE__),
            ]))
        );
    }
}