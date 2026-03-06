<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\WorkOrder;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Controller\Admin\WorkOrderCrudController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
       $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

    return $this->redirect(
        $adminUrlGenerator
            ->setController(WorkOrderCrudController::class)
            ->generateUrl()
    );
    }
    
public function configureDashboard(): Dashboard
{
    return Dashboard::new()
        ->setTitle('OTT Admin'); 
}

public function configureMenuItems(): iterable
{
    yield MenuItem::linkToDashboard('home', 'fa fa-home');
    yield MenuItem::linkToCrud('Usuarios', 'fa fa-user', User::class);
    yield MenuItem::linkToCrud('Órdenes de Trabajo', 'fa fa-wrench', WorkOrder::class);
}
}
