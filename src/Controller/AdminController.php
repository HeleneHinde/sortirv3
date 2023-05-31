<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;

use App\Repository\CampusRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/campus', name: 'campus_list')]
    public function listCampus(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        $campu = new Campus();
        $campuForm = $this->createForm(CampusType::class, $campu);
        $campuForm->handleRequest($request);

        if ($campuForm->isSubmitted() && $campuForm->isValid()) {
            $campusRepository->add($campu, true);

            //après ajout, redirige vers la page de détail
            $this->addFlash('success', 'Campus ajouté !');
            return $this->redirectToRoute('admin_campus_list');

        }

        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
            'campuForm' => $campuForm->createView(),
        ]);
    }

    #[Route('/campus/update/{id}', name: 'campus_update', requirements: ["id" => "\d+"])]
    public function upCampus(Request $request, int $id, CampusRepository $campusRepository): Response
    {

        //affichage des campus
        $campus = $campusRepository->find($id);


        $campuForm = $this->createForm(CampusType::class, $campus);
        $campuForm->handleRequest($request);

        if ($campuForm->isSubmitted() && $campuForm->isValid()) {

            $campusRepository->add($campus, true);

            //après ajout, redirige vers la page de détail

            $this->addFlash('success', 'Campus modifié !');

            return $this->redirectToRoute('admin_campus_list');
        }
        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
            'campuForm' => $campuForm->createView(),
        ]);

    }

    #[Route('/campus/delete/{id}', name: 'campus_delete', requirements: ["id" => "\d+"])]
    public function delCampus(int $id, CampusRepository $campusRepository): Response
    {
        //on récupère le campus à supprimer
        $campus = $campusRepository->find($id);

        //on supprime la série
        $campusRepository->remove($campus, true);

        $this->addFlash('success', $campus->getName() . ' a été supprimé !');


        return $this->redirectToRoute('admin_campus_list');

    }


}
