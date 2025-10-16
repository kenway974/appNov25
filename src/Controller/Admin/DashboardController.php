<?php

namespace App\Controller\Admin;

use App\Entity\{Subscription, User, UserAction, UserNeed, UserSubscription, Notification, Feeling, Need, Action, Block};
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
       return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App Juillet 25');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Actions utilisateurs', 'fas fa-running', UserAction::class);
        yield MenuItem::linkToCrud('Besoins utilisateurs', 'fas fa-heart', UserNeed::class);
        yield MenuItem::linkToCrud('Abonnements utilisateurs', 'fas fa-id-card', UserSubscription::class);
        yield MenuItem::linkToCrud('Abonnements', 'fas fa-receipt', Subscription::class);
        yield MenuItem::linkToCrud('Notifications', 'fas fa-bell', Notification::class);
        yield MenuItem::linkToCrud('Émotions', 'fas fa-smile', Feeling::class);
        yield MenuItem::linkToCrud('Besoins', 'fas fa-hands-helping', Need::class);
        yield MenuItem::linkToCrud('Actions', 'fas fa-bolt', Action::class);
        yield MenuItem::linkToCrud('Blocages', 'fas fa-cubes', Block::class);
    }

}
