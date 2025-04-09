<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    /**
     * @return CartItem[] Returns an array of CartItem objects
     */
    public function findOneByCartAndItem(Cart $cart, Item $item): ?CartItem
    {
        return $this->createQueryBuilder('ci')
            ->andWhere('ci.cart = :cart')
            ->andWhere('ci.item = :item')
            ->setParameter('cart', $cart)
            ->setParameter('item', $item)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCartItemsWithDetails(Cart $cart): array
    {
        return $this->createQueryBuilder('ci')
            ->select('ci, i')
            ->join('ci.item', 'i')
            ->where('ci.cart = :cart')
            ->setParameter('cart', $cart)
            ->getQuery()
            ->getResult();
    }

    public function calculateCartTotal(Cart $cart): float
    {
        $result = $this->createQueryBuilder('ci')
            ->select('SUM(ci.count * i.price) as total')
            ->join('ci.item', 'i')
            ->where('ci.cart = :cart')
            ->setParameter('cart', $cart)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }
} 