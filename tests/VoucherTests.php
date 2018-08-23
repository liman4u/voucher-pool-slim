<?php

namespace Tests;

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Created by PhpStorm.
 * User: liman
 * Date: 8/23/18
 * Time: 9:17 PM
 */
class VoucherTests extends TestCase
{

    public function setUp()
    {
        try {
            (new \Dotenv\Dotenv(__DIR__ . '/../'))->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            //
        }
        $this->http = new \GuzzleHttp\Client([
            'base_uri' => getenv('APP_URL'),
            'exceptions' => false
        ]);

    }

    public function testCanCreateOfferWithValidInformation()
    {
        $request = $this->http->post('/api/v1/offers', [
            'query' => [
                "name" => "Party Time",
                "discount" => 20
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(201,$request->getStatusCode());
        $this->assertSame($body['success'],true);
        $this->assertArrayHasKey('data',$body);
        $this->assertSame($body['data']['name'],"Party Time");

    }

    public function testCanNotCreateOfferWithEmptyName()
    {
        $request = $this->http->post('/api/v1/offers', [
            'query' => [
                "discount" => 25
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertSame($body['success'],false);
        $this->assertEquals(400,$request->getStatusCode());
    }

    public function testCanNotCreateOfferWithEmptyDiscount()
    {
        $request = $this->http->post('/api/v1/offers', [
            'query' => [
                "name" => "Party Time",
                "discount" => ''
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertSame($body['success'],false);
        $this->assertEquals(400,$request->getStatusCode());
    }


    public function testCanGetOffers()
    {
        $request = $this->http->get('/api/v1/offers');
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(200,$request->getStatusCode());
        $this->assertSame($body['success'],true);
        $this->assertArrayHasKey('count',$body);
        $this->assertArrayHasKey('data',$body);
    }


    public function testCanCreateRecipientWithValidInformation()
    {
        $request = $this->http->post('/api/v1/recipients', [
            'query' => [
                "name" => "John Doe",
                "email" => "test@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);

        $this->assertEquals(201,$request->getStatusCode());
        $this->assertSame($body['success'],true);
        $this->assertArrayHasKey('data',$body);
        $this->assertSame($body['data']['name'],"John Doe");

    }

    public function testCanNotCreateRecipientWithEmptyName()
    {
        $request = $this->http->post('/api/v1/recipients', [
            'query' => [
                "email" => "test@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertSame($body['success'],false);
        $this->assertEquals(400,$request->getStatusCode());
    }

    public function testCanNotCreateRecipientWithEmptyEmailAddress()
    {
        $request = $this->http->post('/api/v1/recipients', [
            'query' => [
                "name" => "John doe"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertSame($body['success'],false);
        $this->assertEquals(400,$request->getStatusCode());
    }


    public function testCanGetRecipients()
    {
        $request = $this->http->get('/api/v1/recipients');
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(200,$request->getStatusCode());
        $this->assertSame($body['success'],true);
        $this->assertArrayHasKey('count',$body);
        $this->assertArrayHasKey('data',$body);
    }


    public function testCanGenerateVouchers()
    {
        $request = $this->http->post('/api/v1/vouchers/generate', [
            'query' => [
                "offer_id" => '1',
                "expiry_date" => "2019-08-15 00:00:00"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(201,$request->getStatusCode());
        $this->assertSame($body['success'],true);
        $this->assertGreaterThan(0,$body['count']);
        $this->assertArrayHasKey('data',$body);
    }

    public function testCanNotGenerateVouchersWithEmptyExpiryDate()
    {
        $request = $this->http->post('/api/v1/vouchers/generate', [
            'query' => [
                "offer_id" => 1
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
        $this->assertSame($body['success'],false);
    }

    public function testCanNotGenerateVouchersWithInvalidOffer()
    {
        $request = $this->http->post('/api/v1/vouchers/generate', [
            'query' => [
                "offer_id" => 130,
                "expiry_date" => "2019-08-15 00:00:00"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
        $this->assertSame($body['success'],false);
    }

    public function testCanNotValidateVoucherWithInvalidVoucherCode()
    {
        $request = $this->http->post('/api/v1/vouchers/validate', [
            'query' => [
                "code" => "123456",
                "email" => "test@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(403,$request->getStatusCode());
        $this->assertSame($body['success'],false);
        $this->assertArrayHasKey('message',$body);
    }
    public function testCanNotValidateVoucherWithInvalidEmailInput()
    {
        $request = $this->http->post('/api/v1/vouchers/validate', [
            'query' => [
                "code" => "123456",
                "email" => "test123@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
        $this->assertSame($body['success'],false);

    }


    public function testCanGetRecipientVoucherCodes()
    {
        $request = $this->http->get('/api/v1/vouchers/recipient?email=test@gmail.com');
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(200,$request->getStatusCode());
        $this->assertSame($body['success'],true);
        $this->assertArrayHasKey('count',$body);
        $this->assertArrayHasKey('data',$body);
    }

}