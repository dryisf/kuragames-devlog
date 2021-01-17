<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $fakerEn = \Faker\Factory::create('en_US');

        for ($i=0; $i < 10; $i++) {
            $article = new Article();
            $content ='<p>';
            $content .= join($faker->paragraphs(3), '</p><p>') . '</p>';
            $content_english = '<p>';
            $content_english .= join($fakerEn->paragraphs(3), '</p><p>') . '</p>';
            $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setContentEnglish($content_english)
                    ->setVideo("https://www.youtube.com/watch?v=9gFhvApgM20")
                    ->setCreatedAt(new \DateTime());

            $manager->persist($article);

            $comment = new Comment();
            $comment->setAuthor($faker->sentence())
                    ->setContent(join($faker->paragraphs(1)))
                    ->setIsVerified(false);

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
