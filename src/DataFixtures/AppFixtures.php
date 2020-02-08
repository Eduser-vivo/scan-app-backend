<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Fiche;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public $encode;


    public function __construct(UserPasswordEncoderInterface $encode)
    {
        $this->encode = $encode;
    }


    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
    }


    public function loadUser(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@fiche.com');

        $user->setPassword($this->encode->encodePassword(
            $user, 
            'adminpass'
        ));

        $this->addReference('user_admin', $user);
      
        $manager->persist($user);
        $manager->flush();
    }
}
