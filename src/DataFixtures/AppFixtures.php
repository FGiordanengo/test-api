<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Note;
use App\Entity\Eleve;
use Faker\Provider\DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $matieres = ['Maths', 'Informatique', 'Histoire', 'Sport'];

        for ($e = 1; $e <= 5; $e++) {
            $eleve = (new Eleve())
                    ->setNom($faker->lastname)
                    ->setPrenom($faker->firstname)
                    ->setDateDeNaissance($faker->dateTimeBetween('2000-01-01', '2015-12-31'));

            $this->addReference('eleve' . $e, $eleve);
            $manager->persist($eleve);
        }
        for ($n = 0; $n <=  15; $n++) {
            $matiere = $faker->randomElement($matieres);

            $note = (new Note())
                    ->setValeur(mt_rand(5, 20))
                    ->setMatiere($matiere);

            $eleve = $this->getReference('eleve'.mt_rand(1,5));
            $note->setEleve($eleve);

            $manager->persist($note);
        }

        $manager->flush();
    }
}
