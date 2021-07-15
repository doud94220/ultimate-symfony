<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;

        $hash = $this->encoder->encodePassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("Admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $this->encoder->encodePassword($user, "password");
            $user->setEmail("User$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);
            $manager->persist($user);
        }

        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category->setName($faker->department()) //faut mbezhanov
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product;
                $product->setName($faker->productName()) //faut mbezhanov
                    ->setPrice($faker->price(4000, 20000)) //faut la class de Lior sur les faker prices
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph()) //Faut php/faker je crois
                    ->setMainPicture($faker->imageUrl(400, 400, true)); //Faut PicsumPhotosProvider

                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
