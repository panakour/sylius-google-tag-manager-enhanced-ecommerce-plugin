<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\ProductIdentifierHelper;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object\ProductDetailInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\AddTransaction;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManager;

/**
 * @covers \StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\AddTransaction
 */
final class AddTransactionTest extends TestCase
{
    public function testSimpleTransaction(): void
    {
        // Requirements
        $gtm = new GoogleTagManager(true, 'id1234');

        $productIdentifierHelper = new ProductIdentifierHelper(ProductDetailInterface::ID_IDENTIFIER);

        $order = new Order();

        // Mocks
        $currencyContext = $this->getMockBuilder(CurrencyContextInterface::class)->getMock();
        $channelContext = $this->getMockBuilder(ChannelContextInterface::class)->getMock();

        // Exceptations
        $channelContext->method('getChannel')->willReturn(new Channel());

        // Build object
        $service = new AddTransaction(
            $gtm,
            $channelContext,
            $currencyContext,
            $productIdentifierHelper,
        );

        // Run add
        $service->addTransaction($order);

        // Test result
        $push = $gtm->getPush();
        $lastPush = array_pop($push);

        $this->assertEquals('purchase', $lastPush['event']);
        $this->assertArrayHasKey('ecommerce', $lastPush);
        $this->assertArrayHasKey('currency', $lastPush['ecommerce']);
        $this->assertArrayHasKey('items', $lastPush['ecommerce']);
    }
}
