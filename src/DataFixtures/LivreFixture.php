<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Livre;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LivreFixture extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
      $this->passwordEncoder = $passwordEncoder;  
    }
    
    public function load(ObjectManager $manager): void
    {

       
        $faker = Factory::create("fr_FR");
        for($i = 0;$i<5;$i++){
            $user = new User();
            $user->setNom($faker->lastName)
                ->setPrenom($faker->firstName)
                ->setBirthDate($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'))
                ->setAdresse($faker->streetAddress)
                ->setCodeP($faker->postcode)
                ->setEmail($faker->email)
                ->setPassword($this->passwordEncoder->encodePassword($user,"azerty"))          
                ->setAvatar("https://picsum.photos/seed/picsum/200/300");
               

                     
                $manager->persist($user);
                for($j=0;$j<rand(2,4);$j++){
                    
                    $livre = new Livre();
                    $livre->setAuteur($faker->name)
                            ->setTitre($faker->title)
                            ->setDateSortie($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'));
                    $livre->setUser($user);
                    $manager->persist($livre);

                }
           
        }

        $manager->flush();
    }
}
