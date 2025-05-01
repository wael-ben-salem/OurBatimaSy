<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserAbonnementRepository;  // <-- The critical import

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
    return $this->redirect($routeBuilder->setController(UserAbonnementCrudController::class)->generateUrl());
    }
    #[Route('/admin/stats', name: 'admin_stats')]
    public function statistics(UserAbonnementRepository $repo): Response
    {
        $distribution = $repo->getSubscriptionDistribution();
        $monthlyTrends = $repo->getMonthlySubscriptionTrends();
        $mostPopular = $repo->getMostPopularSubscription();
        $highestRevenue = $repo->getHighestRevenueSubscription();
        $activeSubscriptions = $repo->countActiveSubscriptions();

        // Process data for line chart
        $lineChartData = [];
        $months = range(1, 12);
        
        foreach ($distribution as $type) {
            $lineChartData[$type['name']] = array_fill(1, 12, 0);
        }
        
        foreach ($monthlyTrends as $trend) {
            $lineChartData[$trend['name']][$trend['month']] = $trend['count'];
        }
    
        return $this->render('adminaboonement/stats.html.twig', [
            'distribution' => $distribution,
            'lineChartData' => $lineChartData,
            'months' => $months,
            'most_popular' => $mostPopular,
        'highest_revenue' => $highestRevenue,
        'active_subscriptions' => $activeSubscriptions,

        ]);
    }
}
