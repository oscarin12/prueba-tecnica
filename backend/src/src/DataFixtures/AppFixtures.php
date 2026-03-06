<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\WorkOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = (new User())
            ->setName('Administrador')
            ->setEmail('admin@ott.cl')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin123!'));
        $manager->persist($admin);

        // Técnicos
        $techs = [];
        for ($i = 1; $i <= 3; $i++) {
            $tech = (new User())
                ->setName("Técnico {$i}")
                ->setEmail("tech{$i}@ott.cl")
                ->setRoles(['ROLE_TECH']);
            $tech->setPassword($this->passwordHasher->hashPassword($tech, 'Tech123!'));
            $manager->persist($tech);
            $techs[] = $tech;
        }

        // Órdenes
        $statuses = [
            WorkOrder::STATUS_PENDING,
            WorkOrder::STATUS_IN_PROGRESS,
            WorkOrder::STATUS_DONE,
        ];

        for ($i = 1; $i <= 10; $i++) {
            $wo = (new WorkOrder())
                ->setTitle("Orden #{$i}")
                ->setDescription("Descripción de la orden #{$i}")
                ->setStatus($statuses[($i - 1) % count($statuses)])
                ->setAssignedTo($techs[($i - 1) % count($techs)]);
            $manager->persist($wo);
        }

        $manager->flush();
    }
}