<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\ArticleType;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Controller\HomeController;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Datetime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
         $description = $faker->text(); // generation d'un text
         $dateCreation = self::createDate(); // generation d'une dateCreation
         $article = new Article();
         $article->setTitle($title); // mise a jour du titre
         $article->setDescription($description);// mise a jour de la description
         $article->setDateCreation($dateCreation);// mise a jour de la date de creation
         $article->setPrice( $faker->numberBetween(5,100));// mise a jour de la date de creation
         return $article;
     }



    /* creation et insertion d'un article dans la base de donneroute ménant a un message reussite
      &- creation de function avec  public function insertDate(EntityManagerInterface $entityManager)
      2- creer un article
      3- appel de la methode persist sur article et ensuite flush pour envoyer ds la bd
      4- message vers la bd
    */

    /**
     * @Route("/addArticle", name="addArticle")
     */

    public function insertArticle(EntityManagerInterface $entityManager){

        $repository = $entityManager->getRepository(Category::class);
        $listCategory =  $repository->findAll();
       $choix =  array_rand($listCategory); // choix d une cle de la table
       $choix = $listCategory[$choix];  // recuperation de la valeur de l indice choisi

        $article = self::createArticle();
        $article->setCategory($choix); // mise a jour du choix de la categorie sachant que la categorie_id recois un objet de type categorie
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->render('home/index.html.twig',
            ["message" => "création de l'article  et insertion reussie"]
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
        //dd($article);

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


    /* dans le cas le cas des requestes automatique avec parametre vers une routes , on ne declare pas l entity manager
       et on on utilise SensioFrameworkExtraBundle et on installe avec la commande : composer require sensio/framework-extra-bundle
     en paramete on met la CLASSE en parametre */

    /**
     * @Route("/article/{id}", name="currentArticle")
     */
    public function currentArticle(Article $article): Response
    {
        //$id,EntityManagerInterface $entityManager
       // $repository = $entityManager->getRepository(Article::class);
      //  $article =  $repository->find($id);
        //self::print_q($article);

        return $this->render('article/currentArticle.html.twig', [
            'article' => $article
        ]);
    }


    /* route menant a un article precis */
    /* selection d un article
     1- public function article(EntityManagerInterface $entityManager): Response
    2- reception de l id via le lien de la page twig transmis a la route du nom name
     2-  $nom du repository = $entityManager->getRepository(nom de l entite::class);
     3 - resultat de l article par appel de la fonction la methode sql cree ou une requete préparée ici find($id) */

    /**
     * @Route("/word/{word}", name="magicArticle")
     */
    public function magicArticle($word,EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class); //

        $listArticle =  $repository->findByWord($word);
        $article = array_chunk($listArticle,4,false);
       ///dd($article);

        return $this->render('article/magicArticle.html.twig', [
            'articles' => $article
        ]);
    }


    /* route pour gestion des dates de creation*/
    /**
     * @Route("/annee/{annee}", name="anneeArticle")
     */
    public function anneeArticle($annee,EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class); //

        $listArticle =  $repository->findByDate($annee);
        $article = array_chunk($listArticle,4,false);
         //dd($article);

        return $this->render('article/magicArticle.html.twig', [
            'articles' => $article
        ]);
    }

    /* function update
     1- en parametre , l id l objet et l entity manager
     2- recuperation de l objet via le repository
     3- changement du vote et flush dans la bd sans le persist car l objet est reconnu par doctrine
    */


    /**
     * @Route("/vote/{id}", name="vote", methods="POST")
     */

    public function vote($id,Article $article,Request $request ,EntityManagerInterface $entityManager): Response
    {
        /*self::print_q( $action);
         $repository= $entityManager->getRepository(Article::class);  // declaration de la valeur pour le requete select
         $dataArticle =  $repository->findById($id);  // execution de la requete et recuperation de la valeur de l article concernée
        $dataArticle = $dataArticle[0];   // récupération de la valeur de la clé afin d'obtenir l'objet
        //ici
       // $action === "add" ?  $dataArticle->setVote( $dataArticle->getVote()+1) : $dataArticle->setDislike($dataArticle->getDislike()+1);
       */


        $action = $request->request->all(); // recuperation de la valeur de l'action qui est un tableau
         $action = $action["action"];  // recuperation de la valeur de la cle

        // condition tertiaire afin d ajouter un vote negatif or positif
         $action === "add" ?  $article->setVote( $article->getVote()+1) : $article->setDislike($article->getDislike()+1);

       $entityManager->flush();

        //self::print_q($article);

        return $this->redirectToRoute('currentArticle', [
            'id' => $article->getId()
        ]);
    }// redirection vers le nom de la route qui affiche ca


    /**
     * @Route("/articleRegister", name="articleRegister")
     */
    public function addArticle(Request $request,EntityManagerInterface $entityManager): Response
    {
        // pour le creation du select je selectionne la liste de produit de la bd
        $repository = $entityManager->getRepository(Category::class);
        $categoryList=  $repository->findAllSort();

        $repository = $entityManager->getRepository(Article::class);
        $listeAnnee =  $repository->findYearlist();
        $listeAnnee = array_chunk($listeAnnee,4,false);

       // dd($categoryList);
        $article = new Article(); //creation d un nouvel article

        $form = $this->createForm(ArticleType::class,$article); //creation du formulaire vide ou pre rempli
        $form->handleRequest($request); // recuperation de la valeur du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
           $listData = $form->getData();
          //dd($listData);


            // ... perform some action, such as saving the task to the database
            $entityManager->persist($listData);
            $entityManager->flush();

            return $this->render('home/index.html.twig', [
                'message2' => " article inséré avec succès",
                'listeAnnee' => $listeAnnee,
                'categoryList' => $categoryList

            ]);
        }
/* pour le select , on fait une recherche dans la bd ensuite on l envoi dans l entite car doctrine a cette donnee
 et dans le type utilisé est le type entité et pour ecrire les variable on ne sais pas envoyer un objet donc on cree une fonction
pour cela */
        return $this->renderForm('article/articleForm.html.twig', [
            'category'=> $categoryList,
            'form' => $form
        ]);
    }
}
