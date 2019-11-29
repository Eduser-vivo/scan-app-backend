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

    /**
     * @var Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encode)
    {
        $this->encode = $encode;
        $this->faker = Factory::create();
    }


    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
       $this->loadFiche($manager);
    }

    public function loadFiche(ObjectManager $manager)
    {

        $user = $this->getReference('user_admin');

        for($i = 0; $i < 10; $i++){
            $fiche = new Fiche();
            $fiche->setSignataire($this->faker->realText(15));
            $fiche->setAdresse($this->faker->realText(11));
            $fiche->setCreantier($this->faker->realText(15));
            $fiche->setMontant(154000000);
            $fiche->setMotif($this->faker->realText());
            $fiche->setLieu($this->faker->realText(10));
            $fiche->setDate($this->faker->dateTime);
            $fiche->setUtilisateur($user);
            $manager->persist($fiche);
        }

        $manager->flush();
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
