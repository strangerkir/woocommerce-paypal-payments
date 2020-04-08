<?php
declare(strict_types=1);

namespace Inpsyde\PayPalCommerce\ApiClient;

use Dhii\Data\Container\ContainerInterface;
use Inpsyde\PayPalCommerce\ApiClient\Authentication\Bearer;
use Inpsyde\PayPalCommerce\ApiClient\Endpoint\OrderEndpoint;
use Inpsyde\PayPalCommerce\ApiClient\Factory\AddressFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\AmountFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\ItemFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\LineItemFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\OrderFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\PatchCollectionFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\PayeeFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\PayerFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\PurchaseUnitFactory;
use Inpsyde\PayPalCommerce\ApiClient\Factory\ShippingFactory;
use Inpsyde\PayPalCommerce\ApiClient\Repository\CartRepository;

return [

    'api.host' => function (ContainerInterface $container) : string {
        return 'https://api.sandbox.paypal.com';
    },
    'api.key' => function (ContainerInterface $container) : string {
        return 'AQB97CzMsd58-It1vxbcDAGvMuXNCXRD9le_XUaMlHB_U7XsU9IiItBwGQOtZv9sEeD6xs2vlIrL4NiD';
    },
    'api.secret' => function (ContainerInterface $container) : string {
        return 'EILGMYK_0iiSbja8hT-nCBGl0BvKxEB4riHgyEO7QWDeUzCJ5r42JUEvrI7gpGyw0Qww8AIXxSdCIAny';
    },
    'api.bearer' => function (ContainerInterface $container) : Bearer {
        return new Bearer(
            $container->get('api.host'),
            $container->get('api.key'),
            $container->get('api.secret')
        );
    },
    'api.endpoint.order' => function (ContainerInterface $container) : OrderEndpoint {
        $orderFactory = $container->get('api.factory.order');
        $patchCollectionFactory = $container->get('api.factory.patch-collection-factory');
        return new OrderEndpoint(
            $container->get('api.host'),
            $container->get('api.bearer'),
            $orderFactory,
            $patchCollectionFactory
        );
    },
    'api.cart-repository' => function (ContainerInterface $container) : CartRepository {
        /*
         * ToDo: We need to watch out and see, if we can load the Cart Repository
         * only on specific situations.
         * @see http://ppc-dev-website.localhost/wp-admin/admin.php?page=wc-settings&tab=tax
         */
        $cart = WC()->cart;
        if (! $cart) {
            /**
             *  ToDo: The cart repository gets pulled in the wp-admin,
             *  where there is no WC Cart loaded. Rethink.
             **/
            $cart = new \WC_Cart();
        }
        $factory = $container->get('api.factory.purchase-unit');
        return new CartRepository($cart, $factory);
    },
    'api.factory.purchase-unit' => function (ContainerInterface $container) : PurchaseUnitFactory {

        $amountFactory = $container->get('api.factory.amount');
        $payeeFactory = $container->get('api.factory.payee');
        $itemFactory = $container->get('api.factory.item');
        $shippingFactory = $container->get('api.factory.shipping');
        return new PurchaseUnitFactory(
            $amountFactory,
            $payeeFactory,
            $itemFactory,
            $shippingFactory
        );
    },
    'api.factory.patch-collection-factory' => function (ContainerInterface $container)
        : PatchCollectionFactory {
        return new PatchCollectionFactory();
    },
    'api.factory.payee' => function (ContainerInterface $container) : PayeeFactory {
        return new PayeeFactory();
    },
    'api.factory.item' => function (ContainerInterface $container) : ItemFactory {
        return new ItemFactory();
    },
    'api.factory.shipping' => function (ContainerInterface $container) : ShippingFactory {
        $addressFactory = $container->get('api.factory.address');
        return new ShippingFactory($addressFactory);
    },
    'api.factory.amount' => function (ContainerInterface $container) : AmountFactory {
        return new AmountFactory();
    },
    'api.factory.payer' => function (ContainerInterface $container) : PayerFactory {
        $addressFactory = $container->get('api.factory.address');
        return new PayerFactory($addressFactory);
    },
    'api.factory.address' => function (ContainerInterface $container) : AddressFactory {
        return new AddressFactory();
    },
    'api.factory.order' => function (ContainerInterface $container) : OrderFactory {
        $purchaseUnitFactory = $container->get('api.factory.purchase-unit');
        $payerFactory = $container->get('api.factory.payer');
        return new OrderFactory($purchaseUnitFactory, $payerFactory);
    },
];
