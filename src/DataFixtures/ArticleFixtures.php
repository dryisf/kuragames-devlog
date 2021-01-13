<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i=0; $i < 5; $i++) {
            $article = new Article();
            $content ='<p>';
            $content .= join($faker->paragraphs(3), '</p><p>') . '</p>';
            $article->setTitle($faker->sentence())
            ->setContent($content)
            ->setImage("https://placehold.it/700x300")
            ->setCreatedAt(new \DateTime());

            $manager->persist($article);
        }

        $manager->flush();
    }
}
