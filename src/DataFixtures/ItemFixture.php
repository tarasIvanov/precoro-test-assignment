<?php

namespace App\DataFixtures;

use App\Entity\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ItemFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'name' => 'Ноутбук HP ProBook',
                'price' => 259,
            ],
            [
                'name' => 'Смартфон Samsung Galaxy',
                'price' => 124,
            ],
            [
                'name' => 'Планшет Apple iPad',
                'price' => 189,
            ],
            [
                'name' => 'Навушники Sony WH-1000XM4',
                'price' => 89,
            ],
            [
                'name' => 'Монітор Dell UltraSharp',
                'price' => 159,
            ]
        ];

        foreach ($items as $itemData) {
            $item = new Item();
            $item->setName($itemData['name']);
            $item->setPrice($itemData['price']);

            $manager->persist($item);
        }

        $manager->flush();
    }
}
