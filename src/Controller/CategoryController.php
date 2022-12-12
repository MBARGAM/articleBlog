<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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


    /**
     * @Route("/currentCategory/{category}", name="currentCategory")
     */
    public function currentCategory($category,EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class);
        $listCategory =  $repository->findByCategory($category);
       // dd( $listCategory );
        $article = array_chunk($listCategory ,4,false);

        return $this->render('article/articles.html.twig', [
            'articles' => $article
        ]);
    }

    /**
     * @Route("/categoryRegister" ,name="categoryRegister")
     */



    public function categoryRegister(Request $request,EntityManagerInterface $entityManager){

        $repository = $entityManager->getRepository(Article::class);
        $listeAnnee =  $repository->findYearlist();
        $listeAnnee = array_chunk($listeAnnee,4,false);

        $repository = $entityManager->getRepository(Category::class);
        $categoryList=  $repository->findAllSort();

        $category = new Category();
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $dataCategory = $form->getData();

            $entityManager->persist($dataCategory);
            $entityManager->flush();

            return $this->render('home/index.html.twig',[
                   'message3'=>'categorie inseree avec succès',

                ]

            );
        }

        return $this->renderForm('article/articleForm.html.twig',[
            'titre'=>'Completer le formulaire pour ajouter un article',
            'form' => $form ]);
    }
}




