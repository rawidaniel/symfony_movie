<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MoviesFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('The Godfather');
        $movie->setReleaseYear(1972);
        $movie->setDescription('The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.');
        $movie->setImagePath('https://cdn.pixabay.com/photo/2018/05/02/20/01/baby-3369543_640.jpg');
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));
        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle('Avengers: Endgame');
        $movie2->setReleaseYear(2019);
        $movie2->setDescription('This is the description for Avengers: Endgame.');
        $movie2->setImagePath('https://pixabay.com/photos/cosplay-fantasy-assassins-creed-7157776/');
        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));
        $manager->persist($movie2);

        $manager->flush();

        $manager->flush();
    }
}
