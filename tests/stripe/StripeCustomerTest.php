<?php

namespace Crosspay\Test\Stripe;

use Crosspay\CrossPay;
use Crosspay\Test\CardBrand;
use Crosspay\Test\TestCard;
use Crosspay\Test\TestCase;

class StripeCustomerTest extends TestCase
{
    use TestCard;

    /** @var CrossPay */
    protected $crossPay;

    public static function setUpBeforeClass()
    {
        self::authorizeFromEnv();
    }

    protected function setUp()
    {
        $this->crossPay = new CrossPay([
            'provider' => 'stripe',
            'api_key' => getenv('STRIPE_KEY'),
            'api_secret' => getenv('STRIPE_SECRET'),
            'api_version' => getenv('STRIPE_API_VERSION')
        ]);
    }

    protected function tearDown()
    {

    }

    public function testCreate()
    {
        $email = 'test@example.com';
        $customer = $this->crossPay->customer()->create([
            'email' => 'test@example.com',
            'source' => $this->normalCardStripeToken(CardBrand::Visa()),
        ]);

        $this->assertNotNull($customer);
        $this->assertNotNull($customer->id());
        $this->assertNotNull($customer->created());

        $this->assertEquals($customer->email(), $email);
        $this->assertEquals($customer->defaultCard()->brand(), CardBrand::Visa);
        $this->assertEquals($customer->defaultCard()->last4(), '4242');

        $amount = 1000;
        $currency = 'jpy';
        $chargeDescription = 'test charge';
        $charge = $this->crossPay->charge()->create([
            'customer' => $customer->id(),
            'amount' => $amount,
            'currency' => $currency,
            'description' => $chargeDescription
        ]);

        $this->assertNotNull($charge);
        $this->assertNotNull($charge->id());
        $this->assertNotNull($charge->created());

        $this->assertEquals($charge->currency(), $currency);
        $this->assertEquals($charge->amount(), $amount);
        $this->assertEquals($charge->customerId(), $customer->id());
        $this->assertEquals($charge->description(), $chargeDescription);
        $this->assertEquals($charge->card()->brand(), CardBrand::Visa);
        $this->assertEquals($charge->card()->last4(), '4242');

        $this->assertTrue($charge->captured());
        $this->assertFalse($charge->refunded());
    }

}
