<?php

namespace App\Controller;

use App\Entity\AbsencePeriod;
use App\Entity\MonthlyPeriod;
use App\Form\Type\AbsencePeriodType;
use App\Service\AbsencePeriodService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AbsencePeriodController extends AbstractController
{
    const ABSENCE_BY_PAGE = 20;

    /**
     * @param EntityManagerInterface $em
     * @param AbsencePeriodService $absencePeriodService
     */
    public function __construct(public EntityManagerInterface $em, private AbsencePeriodService $absencePeriodService)
    {
    }

    /**
     * @return Response
     */
    #[Route('/absence', name: 'absence_period')]
    public function index(): Response
    {
        return $this->render('absence_period/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/absence/new', name: 'absence_period_create')]
    public function create(Request $request): Response
    {
        $absencePeriod = new AbsencePeriod();
        $form = $this->createForm(AbsencePeriodType::class, $absencePeriod);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($absencePeriod);
                $this->em->flush();
                $this->addFlash('success', 'L\'absence a bien été enregistrée');
            } else {
                $this->addFlash('danger', 'Le formulaire comporte des erreurs.');
            }
        }
        return $this->render('absence_period/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/absence/filter', name: 'absence_period_filter')]
    public function filter(Request $request): Response
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        if (($month = $request->query->get('month')) && ($year = $request->query->get('year'))) {
            $period = new MonthlyPeriod();
            $period->setMonth($month)->setYear($year);
        }
        $jsonContent = $serializer->serialize($this->absencePeriodService->getAbscencePeriodByPeriod($period ?? null, self::ABSENCE_BY_PAGE), 'json');
        return new Response($jsonContent);
    }
}
