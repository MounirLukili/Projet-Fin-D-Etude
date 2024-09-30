<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\ScoreRepository;
use App\Service\PDFService;

class StudentScoresController extends AbstractController
{
    private $userRepository;
    private $scoreRepository;
    private $pdfService;

    public function __construct(UserRepository $userRepository, ScoreRepository $scoreRepository, PDFService $pdfService)
    {
        $this->userRepository = $userRepository;
        $this->scoreRepository = $scoreRepository;
        $this->pdfService = $pdfService;
    }

    #[Route('/admin/student-scores', name: 'admin_student_scores')]
    public function index(Request $request)
    {
        $user = $this->getUser(); // Ensure admin permissions
        $allUsers = $this->userRepository->findAll();
        $students = array_filter($allUsers, function($user) {
            return $user->getRole() === "ROLE_STUDENT";
        });

        if ($request->isMethod('POST')) {
            $userId = $request->request->get('student');
            $student = $this->userRepository->find($userId);
            $scores = $this->scoreRepository->findScoresByStudent($userId);

            $subjects = [];
            $scoreDetails = [];
            foreach ($scores as $score) {
                $subjectName = $score->getSujet()->getModule();
                $scoreDetails[] = [
                    'module' => $subjectName,
                    'note' => $score->getNote(),
                    'date' => $score->getDate()->format('Y-m-d'),
                    'difficulty' => $score->getNiveau()
                ];

                if (!isset($subjects[$subjectName])) {
                    $subjects[$subjectName] = ['total' => 0, 'count' => 0];
                }
                $subjects[$subjectName]['total'] += $score->getNote();
                $subjects[$subjectName]['count']++;
            }

            foreach ($subjects as $module => &$data) {
                $data = [
                    'module' => $module,
                    'average' => $data['total'] / $data['count']
                ];
            }

            $pdf = $this->pdfService->createScoresPDF($student->getNom(), $student->getPrenom(), array_values($subjects), $scoreDetails);
            return new Response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="student_scores.pdf"',
            ]);
        }

        return $this->render('admin/temp.html.twig', [
            'students' => $students
        ]);
    }
}
