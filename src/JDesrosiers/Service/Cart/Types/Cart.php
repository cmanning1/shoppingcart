<?php

namespace JDesrosiers\Service\Cart\Types;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\XmlRoot("Cart")
 */
class Cart
{
    /**
     * @Serializer\Type("string")
     * @Assert\Type("string")
     * @Assert\NotNull()
     */
    protected $cartId;

    /**
     * @Serializer\Type("DateTime")
     * @Assert\DateTime()
     * @Assert\NotNull()
     */
    protected $createdDate;

    /**
     * @Serializer\Type("DateTime")
     * @Assert\DateTime()
     */
    protected $completedDate;

    /**
     * @Serializer\Type("array<CartItem>")
     * @Serializer\XmlList(inline = false, entry = "cartItem")
     * @Assert\Type("array")
     * @Assert\NotNull()
     */
    protected $cartItems = array();

    public function __construct(array $cart)
    {
        if (array_key_exists('cartId', $cart)) {
            $this->cartId = $cart['cartId'];
            $this->createdDate = $cart['createdDate'];
        } else {
            $this->cartId = substr(md5(microtime(true)), 0, 12);
            $this->createdDate = new DateTime();
        }
        
        if (array_key_exists('completedDate', $cart)) {
            $this->completedDate = $cart['completedDate'];
        }

        if (array_key_exists('cartItems', $cart)) {
            foreach ($cart['cartItems'] as $cartItemId => $cartItem) {
                $this->cartItems[$cartItemId] = new CartItem($cartItem);
            }
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function addCartItem(CartItem $cartItem)
    {
        $cartItem->setCartItemId(substr(md5(microtime(true)), 0, 12));
        $this->cartItems[$cartItem->cartItemId] = $cartItem;

        return $cartItem->cartItemId;
    }
}
