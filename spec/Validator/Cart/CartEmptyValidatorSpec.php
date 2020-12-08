<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\Validator\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\CartEmpty;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CartEmptyValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $repository,
        ExecutionContextInterface $context
    ): void {
        $this->beConstructedWith($repository);
        $this->initialize($context);
    }

    function it_add_no_violation_if_cart_is_not_empty(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        ArrayCollection $collection,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $order->getItems()->willReturn($collection);
        $collection->isEmpty()->willReturn(false);

        $context->addViolation('sylius.shop_api.checkout.cart.empty')->shouldNotBeCalled();

        $this->validate('CART_TOKEN', new CartEmpty());
    }

    function it_add_violation_if_cart_is_empty(
        OrderRepositoryInterface $repository,
        OrderInterface $order,
        ArrayCollection $collection,
        ExecutionContextInterface $context
    ): void {
        $repository->findOneBy(['tokenValue' => 'CART_TOKEN', 'state' => OrderInterface::STATE_CART])->willReturn($order);

        $order->getItems()->willReturn($collection);
        $collection->isEmpty()->willReturn(true);

        $context->addViolation('sylius.shop_api.checkout.cart.empty')->shouldBeCalled();

        $this->validate('CART_TOKEN', new CartEmpty());
    }
}
