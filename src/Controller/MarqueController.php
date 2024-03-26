<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'app_marque')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($logoFile) {
                $newFilename = uniqid() . '.' . $logoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', "Impossible d'ajouter le logo");
                    return $this->redirectToRoute('app_marque');
                }

                // updates the 'logoFilename' property to store the PDF file name
                // instead of its contents
                $marque->setLogo($newFilename);
            }

            $em->persist($marque);
            $em->flush();

            $this->addFlash('success', 'Marque ajoutée');
            return $this->redirectToRoute('app_marque');
        }

        $marques = $em->getRepository(Marque::class)->findAll();

        return $this->render('marque/index.html.twig', [
            'marques' => $marques,
            'ajout' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_marque_show')]
    public function show(Marque $marque, EntityManagerInterface $em, Request $request): Response
    {

        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($marque->getLogo() != null) {
                unlink($this->getParameter('upload_directory') . '/' . $marque->getLogo());
            }

            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($logoFile) {
                $newFilename = uniqid() . '.' . $logoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', "Impossible d'ajouter le logo");
                    return $this->redirectToRoute('app_marque');
                }

                // updates the 'logoFilename' property to store the PDF file name
                // instead of its contents
                $marque->setLogo($newFilename);
            }

            $em->persist($marque);
            $em->flush();

            $this->addFlash('success', 'Marque ajoutée');
            return $this->redirectToRoute('app_marque');
        }

        return $this->render('marque/show.html.twig', [
            'marque' => $marque,
            'edit' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'app_marque_delete')]
    public function delete(Marque $marque, EntityManagerInterface $em): Response
    {
        $em->remove($marque);
        $em->flush();

        $this->addFlash('danger', 'Marque supprimée');
        return $this->redirectToRoute('app_marque');
    }
}
