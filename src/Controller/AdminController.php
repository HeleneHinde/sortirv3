<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

   // #[IsGranted("ROLE_ADMIN")]
    #[Route('/campus', name: 'campus_list')]
    public function listCampus(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();




        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/campus/update/{id}', name: 'campus_update', requirements: ["id" => "\d+"])]
    public function upCampus(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();




        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/campus/delete/{id}', name: 'campus_delete', requirements: ["id" => "\d+"])]
    public function delCampus(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();




        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/campus', name: 'campus_add', requirements: ["id" => "\d+"])]
    public function addCampus(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();




        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
        ]);
    }
}
