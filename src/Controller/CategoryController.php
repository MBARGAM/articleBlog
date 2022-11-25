<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory;

class CategoryController extends AbstractController
{
    //creation d'une fonction statique d'affichage
    static function print_q($val){
        echo "<pre style='background-color:#000;color:#3FBBD5;font-size:11px;z-index:99999;position:relative;'>";
        print_r($val);
        echo "</pre>";
    }

    /*function permettant de creer une date*/
    static function createCategory(){
        $faker = Factory::create();
        $category = $faker->jobTitle();
        return $category ;
    }
/* ici je cree une classe category et je l insere dans la base de donnee et ensuite
   si l insertion est ok , je redirigire vers la route de selection des categorie de la table categorie*/
    /**
     * @Route("/category", name="category")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $data = self::createCategory();
        $category = new Category();
        $category->setName($data);
       // self::print_q($category);
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->render('home/index.html.twig', [
            'msg' => 'Catégorie crée avec succès '
        ]);
    }


}
