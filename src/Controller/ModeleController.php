<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Form\ModeleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/modele')]
class ModeleController extends AbstractController
{
    #[Route('/', name: 'app_modele')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $modele = new Modele();
        $form = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($modele);
            $em->flush();

            $this->addFlash('success', 'Modèle ajouté');
            return $this->redirectToRoute('app_modele');
        }

        $modeles = $em->getRepository(Modele::class)->findAll();

        return $this->render('modele/index.html.twig', [
            'modeles' => $modeles,
            'ajout' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_modele_show')]
    public function show(Modele $modele, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($modele);
            $em->flush();

            $this->addFlash('success', 'Modèle modifié');
            return $this->redirectToRoute('app_modele');
        }

        return $this->render('modele/show.html.twig', [
            'modele' => $modele,
            'edit' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'app_modele_delete')]
    public function delete(Modele $modele, EntityManagerInterface $em): Response
    {
        $em->remove($modele);
        $em->flush();

        $this->addFlash('danger', 'Modele supprimé');
        return $this->redirectToRoute('app_modele');
    }
}
