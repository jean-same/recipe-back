<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Difficulty;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Picture;
use App\Entity\Type;
use App\Repository\IngredientRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    protected $passwordHasher;
    protected $ingredientRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher , IngredientRepository $ingredientRepository )
    {
        $this->passwordHasher = $passwordHasher;
        $this->ingredientRepository = $ingredientRepository;
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('en_US');
        $faker->addProvider(new \Bezhanov\Faker\Provider\Food($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));


        $roleArray = [
            "ROLE_CONTRIBUTOR",
            "ROLE_CHIEF",
            "ROLE_ADMIN",
        ];

        $ingredientParentArray = [
            [
                "name" => "Sucré",
                "description" => "Que du sucre"
            ],
            [
                "name" => "Salé",
                "description" => "Que du sel"
            ],
            [
                "name" => "Mixte",
                "description" => "Melange salé sucré"
            ]
        ];

        $ingredientArray = [
            "1 bouquet garni",
            "sel et poivre",
            "1 oignon",
            "2 cuillères à soupe de crème fraîche",
            "20 gr de beurre",
            "500 gr de pois cassés",
            "4 c. soupe d'olives vertes dénoyautées",
            "4 c. soupe de concentré de tomate",
            "1 c. à café de sucre en poudre",
            "2 c. soupe d'huile d'olive",
            "1/2 c. à café de purée de piment",
            "1 c. à café de cumin",
            "500 g viande de bœuf hachée",
            "1 jaune d'oeuf et 1 c. soupe de lait pour dorer les empanadas",
            "100 grammes de beurre fondu",
            "60 grammes de poudre d’amandes",
            "1 sachet de levure chimique",
            "Pistils de safran",
            "180 grammes de sésame grillé moulu",
            "10cl d’eau de fleur d’oranger",
            "1 betterave",
            "une pointe de muscade",
            "4 belles pommes de terre",
            "1 bouquet de thym",
            "3 ailes et 3 cuisses de canard confites",
            "900 g de haricots blancs"
        ];

        $recipeArray = [
            [
                "title" => "Purée de betteraves",
                "description" => "Vous connaissez sûrement les chebakia, ces petits gâteaux enrobés de miel que l'on déguste lors de la période du ramadan. Voici une recette de chebakia facile afin de faire voyager vos papilles dans les délices des pâtisseries orientales ! "
            ],
            [
                "title" => "Purée de pois cassés",
                "description" => "Les empanadas sont de petits chaussons farcis typiques de la cuisine argentine que l'on trouve également dans toute l'Amérique latine. Chaque pays, chaque région et même chaque famille ont leurs recettes d'empanadas dont la forme ainsi que la composition de la farce peut varier, tout comme celle de la pâte. Cette recette d'empanadas facile est préparée avec de la pâte prête à l'emploi et une farce classique d'empanadas carne à la viande. "
            ],
            [
                "title" => "Quiche",
                "description" => "Vous connaissez sûrement les chebakia, ces petits gâteaux enrobés de miel que l'on déguste lors de la période du ramadan. Voici une recette de chebakia facile afin de faire voyager vos papilles dans les délices des pâtisseries orientales ! "
            ],
            [
                "title" => "Chebakia facile",
                "description" => "LRecouvrez la plaque du four avec du papier sulfurisé, déposez les empanadas facile et badigeonnez-les au pinceau de cuisine avec du jaune d'oeuf battu avec le lait. "
            ],
            [
                "title" => "Empanadas facile ",
                "description" => "Dressez votre cassoulet de canard. Frottez d'ail un plat à four en terre. Déposez-y les ailes et les cuisses de canard confites, les saucissons à cuire, le lard coupé en dés. "
            ],
            [
                "title" => "Cassoulet au canard ",
                "description" => "Enfournez pour 20 à 25 minutes selon votre four. Les empanadas doivent être dorés. Vous pouvez servir ces empanadas à la viande chauds ou bien froids, accompagnés par exemple d'une salade de saison.  "
            ]
        ];

        $typeArray = [
            "Entrees",
            "Plat",
            "Dessert",
            "Apperitif"
        ];

        $difficultyArray = [
            "Facile",
            "Moyen",
            "Difficile"
        ];

        $allUsers = [];

        for($u = 0; $u < 25; $u++) {
            $user = new User;
            $hash = $this->passwordHasher->hashPassword($user, "password"); 

            $user->setPseudo($faker->promotionCode())
                ->setEmail($faker->email())
                ->setPassword(($hash))
                ->setRoles($faker->randomElements($roleArray));

            $allUsers[] = $user;

            $manager->persist($user);
        }

        $ingredientParentAdded = [];

        foreach($ingredientParentArray as $ingredientParent) {
          
            $ingredientNew = new Ingredient;

            $ingredientNew->setName($ingredientParent["name"])
                    ->setDescription($ingredientParent["description"]);

            $ingredientParentAdded[] = $ingredientNew;
            $manager->persist($ingredientNew);

        }

        $ingredientChildArray = [];
        foreach($ingredientArray as $ingredientChild) {
            $randomIngredientParent = $faker->randomElements($ingredientParentAdded);

            $ingredient = new Ingredient;
            $ingredient->setName($ingredientChild)
                    ->setDescription($faker->realText($faker->numberBetween(10, 20)));

            foreach($randomIngredientParent as $currentIngredientParent) {
                $ingredient->setParent($currentIngredientParent);
            }

            $ingredientChildArray[] = $ingredient;

            $manager->persist($ingredient);
        }

        $typeAddedArray = [];
        foreach($typeArray as $currentType) {
            $type = new Type;

            $type->setName($currentType)
                ->setDescription($faker->text());

            $typeAddedArray[] = $type;
            $manager->persist($type);
        }

        $difficultyAddedArray = [];

        foreach($difficultyArray as $currentDifficulty) {
            $difficulty = new Difficulty;

            $difficulty->setName($currentDifficulty)
                    ->setDescription($faker->text());

            $manager->persist($difficulty);
            $difficultyAddedArray[] = $difficulty;
        }
        
        $allRecipes = [];
        foreach( $recipeArray as $result ) {
            $recipe = new Recipe;

            $nbIngredientToAdd = mt_rand(1, 3);
            $ingredientsToAdd = $faker->randomElements($ingredientChildArray, $nbIngredientToAdd);

            $recipe->setTitle($result["title"] )
                    ->setContent($result["description"])
                    ->setType($faker->randomElements($typeAddedArray)[0] )
                    ->setDifficulty($faker->randomElements($difficultyAddedArray)[0] )
                    ->setUser($faker->randomElements($allUsers)[0] );

            

            foreach($ingredientsToAdd as $ingredientToAdd) {

                $recipe->addIngredient($faker->randomElements($ingredientsToAdd)[0]);
            }

            $picture = new Picture;

            $picture->setUrl("https://baconmockup.com/350/350/")
                    ->setRecipe($recipe);

    
            $allRecipes[] = $recipe;
            $manager->persist($recipe);
            $manager->persist($picture);
        }

        $commentAddedArray = [];

        for($c = 0; $c < 30; $c++) {
            $comment = new Comment;

            $comment->setContent($faker->text() )
                    ->setUser($faker->randomElements($allUsers)[0] )
                    ->setRecipe($faker->randomElements($allRecipes)[0] )
                    ->setCreatedAt(new DateTimeImmutable());

            $commentAddedArray[] = $comment;

            $manager->persist($comment);
        }



       $manager->flush();
    }
}
