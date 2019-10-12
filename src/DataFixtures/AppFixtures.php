<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        $adminUser = new User();
        $adminUser->setFirstname('quentin')
            ->setLastname('Sommesous')
            ->setEmail('quentin.sommesous@sfr.fr')
            ->setHash($this->encoder->encodePassword($adminUser,'password'))->setPicture('https://avatars.io/twitter/sloders')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($adminRole);
        $manager->persist($adminUser);


        $slugify = new Slugify();
        //Nous gerons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];



        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $hash = $this->encoder->encodePassword($user, 'password');

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $user
                ->setFirstname($faker->firstName($genre))
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);


            $manager->persist($user);
            $users[] = $user;
        }
// Nous gerons les annonces
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $slug = $slugify->slugify($title);
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];

            $ad
                ->setTitle($title)
                ->setSlug($slug)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);


            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();

                $image
                    ->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
