<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $user = new User();
        // $manager->persist($user);

        $manager->flush();
    }
}
