<?php

namespace App\Controller\Api;

use App\Entity\WorkOrder;
use App\Repository\WorkOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/work-orders")
 */
class WorkOrderController extends AbstractController
{
    /**
     * @Route("", name="api_work_orders_list", methods={"GET"})
     */
    public function list(Request $request, WorkOrderRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $status = $request->query->get('status');

        $orders = $repo->findAssignedToUser($user, $status);

        return $this->json(array_map(static fn (WorkOrder $o) => [
            'id' => $o->getId(),
            'title' => $o->getTitle(),
            'status' => $o->getStatus(),
            'createdAt' => $o->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $orders));
    }

   /**
 * @Route("/{id}", name="api_work_orders_detail", methods={"GET"})
 */
    public function detail(int $id, WorkOrderRepository $repo): JsonResponse
    {
        $user = $this->getUser();

        $workOrder = $repo->find($id);
        if (!$workOrder) {
            return $this->json(['message' => 'Not found'], 404);
         }

    if ($workOrder->getAssignedTo()?->getId() !== $user?->getId()) {
        return $this->json(['message' => 'Forbidden'], 403);
    }

    return $this->json([
        'id' => $workOrder->getId(),
        'title' => $workOrder->getTitle(),
        'description' => $workOrder->getDescription(),
        'status' => $workOrder->getStatus(),
        'createdAt' => $workOrder->getCreatedAt()->format('Y-m-d H:i:s'),
        'assignedTo' => [
            'id' => $workOrder->getAssignedTo()?->getId(),
            'email' => $workOrder->getAssignedTo()?->getEmail(),
            'name' => $workOrder->getAssignedTo()?->getName(),
        ],
    ]);
}

/**
 * @Route("/{id}/status", name="api_work_orders_update_status", methods={"PATCH"})
 */
public function updateStatus(
    int $id,
    WorkOrderRepository $repo,
    Request $request,
    EntityManagerInterface $em
): JsonResponse {
    $user = $this->getUser();

    $workOrder = $repo->find($id);
    if (!$workOrder) {
        return $this->json(['message' => 'Not found'], 404);
    }

    if ($workOrder->getAssignedTo()?->getId() !== $user?->getId()) {
        return $this->json(['message' => 'Forbidden'], 403);
    }

    $data = json_decode($request->getContent() ?: '{}', true);
    $newStatus = $data['status'] ?? null;

    try {
        if ($newStatus) {
            $workOrder->setStatus($newStatus);
        } else {
            $workOrder->advanceStatus();
        }
    } catch (\InvalidArgumentException $e) {
        return $this->json(['message' => $e->getMessage()], 400);
    }

    $em->flush();

    return $this->json([
        'id' => $workOrder->getId(),
        'status' => $workOrder->getStatus(),
    ]);
}
}