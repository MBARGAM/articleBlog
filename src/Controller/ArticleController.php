<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Datetime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController
{

    //creation d'une fonction statique d'affichage
    static function print_q($val){
        echo "<pre style='background-color:#000;color:#3FBBD5;font-size:11px;z-index:99999;position:relative;'>";
        print_r($val);
        echo "</pre>";
    }

    /* function permettant de creer une date*/
    static function createDate(){
        $faker = Factory::create();
        $date = $faker->date();
        $date = new Datetime($date);

        return $date ;
    }

    /* function permettant de creer un article */

     static function createArticle(){

         $faker = Factory::create();
         $title = $faker->word(); // generation d'un titre
         $description = $faker->text()." magique"; // generation d'un text
         $dateCreation = self::createDate(); // generation d'une dateCreation
         $article = new Article();
         $article->setTitle($title); // mise a jour du titre
         $article->setDescription($description);// mise a jour de la description
         $article->setDateCreation($dateCreation);// mise a jour de la date de creation
         return $article;
     }


    /* route menant a un article */
    /**
     * @Route("/accueil", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    /* creation et insertion d'un article dans la base de donneroute ménant a un message reussite
      &- creation de function avec  public function insertDate(EntityManagerInterface $entityManager)
      2- creer un article
      3- appel de la methode persist sur article et ensuite flush pour envoyer ds la bd
      4- message vers la bd
    */

    /**
     * @Route("/accueil", name="accueil")
     */

    public function insertDate(EntityManagerInterface $entityManager){

        $article = self::createArticle();
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->render('article/index.html.twig',
            ["message" => "insertion reussie "]
        );

    }

    /* route menant a un article */
    /* selection de tout les articles
     1- public function article(EntityManagerInterface $entityManager): Response
     2-  $nom du repository = $entityManager->getRepository(nom de l entite::class);
     3 - resultat de l article par appel de la fonction la methode sql find ou une requete préparée */

    /**
     * @Route("/article", name="articles")
     */
    public function article(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class);

        $listArticle =  $repository->findAll();
         $article = array_chunk($listArticle,4,false);
        //self::print_q($article);

        return $this->render('article/articles.html.twig', [
            'articles' => $article
        ]);
    }

    /* route menant a un article precis */
    /* selection d un article
     1- public function article(EntityManagerInterface $entityManager): Response
    2- reception de l id via le lien de la page twig transmis a la route du nom name
     2-  $nom du repository = $entityManager->getRepository(nom de l entite::class);
     3 - resultat de l article par appel de la fonction la methode sql find ou une requete préparée ici find($id) */

    /**
     * @Route("/article/{id}", name="currentArticle")
     */
    public function currentArticle($id,EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class);

        $article =  $repository->find($id);
        //self::print_q($article);

        return $this->render('article/currentArticle.html.twig', [
            'article' => $article
        ]);
    }

    // route et methode pour recuperer le vote fait en ajax
    // appeler la classe jresponse par  use Symfony\Component\HttpFoundation\JsonResponse;
    // cmde 1 installer//composer require friendsofsymfony/jsrouting-bundle
    //cmdé 2//
    //cmde 3 //php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
    // mettre ce code ds le controlleur   :  <<options={"expose"=true}, methods={"GET"}>>


    /**
     * @Route("/vote/{somme}", name="vote" )
     */
    public function vote($somme): JsonResponse
    {
        $nbreActuel = intval($somme);

        if(is_numeric($nbreActuel)){
            $nbreActuel = $nbreActuel + 1 ;
        }

        return $this->json(['ajout' => $nbreActuel]);
    }


}
