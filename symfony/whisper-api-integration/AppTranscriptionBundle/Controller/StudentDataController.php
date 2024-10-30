<?php

namespace AppTranscriptionBundle\Controller;


use AppTranscriptionBundle\Service\StudentDataService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StudentDataController extends AbstractController
{

    private StudentDataService $studentDataService;
    private LoggerInterface $logger;
    private string $allowedIps;

    public function __construct
    (
        StudentDataService $studentDataService,
        LoggerInterface    $logger,
        string             $allowedIps
    )
    {
        $this->studentDataService = $studentDataService;
        $this->logger = $logger;
        $this->allowedIps = $allowedIps;
    }

    /**
     * @Route("/transcriptions/student-data", name="api_student_data", methods={"GET"})
     */
    public function getStudentData(Request $request): JsonResponse
    {

        $allowedIps = json_decode($this->allowedIps, true);
        $clientIp = $request->getClientIp();

        if (!in_array($clientIp, $allowedIps)) {
            $this->logger->error('Access denied.' . $clientIp);
            return new JsonResponse(['error' => 'Access denied.'], 403);
        }

        $studentsData = $this->studentDataService->getStudentData();

        return new JsonResponse($studentsData);

    }

}