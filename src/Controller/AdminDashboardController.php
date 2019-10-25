<?php

namespace App\Controller;


use App\Service\StatsService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @param ObjectManager $manager
     * @param StatsService $statsService
     * @return Response
     */
    public function index(ObjectManager $manager, StatsService $statsService)
    {
        $statsServices = $statsService->getStats();

        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $statsServices,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
