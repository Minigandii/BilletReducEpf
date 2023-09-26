<?php

namespace App\DataFixtures;

use App\Entity\Administrateur;
use App\Entity\Theatre;
use App\Entity\Ouvreur;
use App\Entity\Pourboire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $theatre = new Theatre();
        $theatre -> setEmail('mailtheater1@gmail.com')
                        -> setPassword('mdptheatre1')
                        -> setNom('Theatre 1')
                        -> setQrCode('path');

        $manager->persist($theatre);
        $manager->flush();

        /* commande pour mettre dans la bdd : php bin/console doctrine:fixtures:load */
    }
}
