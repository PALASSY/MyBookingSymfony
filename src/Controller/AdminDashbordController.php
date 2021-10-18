<?php

namespace App\Controller;

use App\Service\Statistics;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashbordController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashbord")
     */
    public function index(ObjectManager $manager,Statistics $statsService): Response
    {
        //On va récupérer toutes les données des Entity à l'aide de DQL(dépolué dans src/Service/Statistics.php) 
        $stats = $statsService->getStatistics();
        //dd($comments);

        //On va récupérer les meilleurs notes des commentaires dans l'annonce
        $bestAds = $statsService->getAdsStats('DESC');

        //On va récupérer les meilleurs notes des commentaires dans l'annonce
        $worstAds = $statsService->getAdsStats('ASC');

        //dd($worstAds);


        return $this->render('admin/dashbord/index.html.twig', [
            //On va compacter toutes les résultats
            'stats'=>$stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
