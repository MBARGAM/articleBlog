<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    //creation d'une fonction statique d'affichage
    static function print_q($val){
        echo "<pre style='background-color:#000;color:#3FBBD5;font-size:11px;z-index:99999;position:relative;'>";
        print_r($val);
        echo "</pre>";
    }
    /* route menant a un la page avec pour parametre lesd annees recuperés dans la table article  */
    /**
     * @Route("/accueil", name="accueil")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Article::class);

        $listeAnnee =  $repository->findYearlist();
        $listeAnnee = array_chunk($listeAnnee,4,false);

        $repository = $entityManager->getRepository(Category::class);
        $categoryList=  $repository->findAllSort();

       //self::print_q($listeAnnee);
       //dd($categoryList);

        return $this->render('home/index.html.twig', [
            'listeAnnee' => $listeAnnee,
            'categoryList' => $categoryList
        ]);
    }





}
