<?php

namespace mpyw\TestOfCowitter;

require_once __DIR__ . '/../../assets/dummy_curl.php';

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
class APIErrorTest extends \Codeception\TestCase\Test {

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

    public function testBasicErrorHttp()
    {
        $this->setExpectedException(HttpException::class, 'ERROR', 123);
        $this->c->get('errors/basic_error');
    }

    public function testBasicErrorCurl()
    {
        $this->setExpectedException(CURLException::class, 'The requested URL returned error: 400');
        $this->c->withOptions([CURLOPT_FAILONERROR => true])
                ->get('errors/basic_error');
    }

    public function testEmpty()
    {
        $this->setExpectedException(HttpException::class, 'The server returned the status 400 with empty response. (This is a message generated by Cowitter)');
        $this->c->get('errors/empty');
    }

    public function testMalformed()
    {
        $this->setExpectedException(\UnexpectedValueException::class);
        $this->c->get('errors/malformed');
    }

    public function testExpiredRequestToken()
    {
        $this->setExpectedException(HttpException::class, 'Invalid / expired Token');
        $this->c->post('errors/expired_request_token');
    }

    public function testInvalidSignature()
    {
        $this->setExpectedException(HttpException::class, 'Failed to validate oauth signature and token');
        $this->c->post('errors/invalid_signature');
    }

    public function testTimelineUnauthorized()
    {
        $this->setExpectedException(HttpException::class, 'Not authorized.');
        $this->c->post('errors/timeline_unauthorized');
    }

    public function testRetweetFailed()
    {
        $this->setExpectedException(HttpException::class, 'sharing is not permissible for this status (Share validations failed');
        $this->c->post('errors/retweet_failed');
    }

    public function testFailedStreamingHttp()
    {
        $this->setExpectedException(HttpException::class, 'Unauthorized');
        $this->c->streaming('errors/failed_streaming', function () {});
    }


    public function testFailedStreamingHttpHtml()
    {
        $this->setExpectedException(\UnexpectedValueException::class);
        $this->c->streaming('errors/unexpected', function () {});
    }


    public function testFailedStreamingCurl()
    {
        $this->setExpectedException(CURLException::class, 'The requested URL returned error: 401');
        $this->c->withOptions([CURLOPT_FAILONERROR => true])
                ->streaming('errors/failed_streaming', function () {});
    }
}
