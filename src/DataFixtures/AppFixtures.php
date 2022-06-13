<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Notification;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $notificationRepo = $manager->getRepository(Notification::class);
        
        $notification = new Notification([
            'from_user' => 1,
            'to_user' => 2,
            'message' => 'xx xx',
            'status' => Notification::STATUS_REQUEST,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        $notificationRepo->add($notification, true);
    }
}
