<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Form\FicheType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FicheController extends AbstractController
{
    /**
     * @Route("/", name="fiche_acceuil")
     */
    public function acceuilAction(PaginatorInterface $paginator, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Fiche::class);

        $fiches = $paginator->paginate($repository->findAll(), $request->query->getInt('page', 1), 5);

        return $this->render('fiche/acceuil.html.twig', ['fiches' => $fiches]);
    }

    /**
     * @Route("/fiches", name="fiches_with_filtre")
     */
    public function fichesWithFiltre(PaginatorInterface $paginator, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Fiche::class);

        $session = new Session();
        $session->start();

        if ($request->isMethod('POST')) {
            $date1 = $request->request->get('date1');
            $date2 = $request->request->get('date2');
            $session->set('date1', $date1);
            $session->set('date2', $date2);
        }

        $date1 = $session->get('date1');
        $date2 = $session->get('date2');
        $fiches = $paginator->paginate($repository->findFicheWithDate($date1, $date2), $request->query->getInt('page', 1), 3);
        $count = count($fiches);

        return $this->render('fiche/filtre.html.twig', [
            'fiches' => $fiches,
            'count' => $count,
            'date1' => $date1,
            'date2' => $date2,
            ]);
    }

    /**
     * @Route("/form", name="form_action")
     */
    public function index(Fiche $fiche = null, Request $request)
    {
        if (!$fiche) {
            $fiche = new Fiche();
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(FicheType::class, $fiche);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $fiche = $form->getData();
                $fiche->setDate(new \DateTime());

                $em->persist($fiche);
                $em->flush();

                return $this->redirectToRoute('fiche_affiche', ['id' => $fiche->getId()]);
            }
        }

        return $this->render('fiche/form.html.twig', [
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/fiche/{id}", name="fiche_affiche",  requirements={"id"="\d+"})
     */
    public function ficheAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository(Fiche::class)->find($id);

        if (null === $fiche) {
            throw new NotFoundHttpException("l'annonce d'id ".$id." n'existe pas .");
        }

        return $this->render('fiche/fiche.html.twig', ['formData' => $fiche]);
    }

     /**
     * @Route("/fiche/{id}", name="pdf_action",  requirements={"id"="\d+"})
     */
    public function pdfAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository(Fiche::class)->find($id);

        if (null === $fiche) {
            throw new NotFoundHttpException("l'annonce d'id ".$id." n'existe pas .");
        }

        $html = $this->renderView('fiche/fiche.html.twig', [
            'formData'=>$fiche,
        ]);

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 'fiche.pdf'
        );
    }

    /**
     * @Route("/edit/{id}", name="edit_action", requirements={"id"="\d+"})
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository(Fiche::class)->find($id);

        if (null === $fiche) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
        }

        $form = $this->createForm(FicheType::class, $fiche);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('fiche_affiche', ['id' => $fiche->getId()]);
            }
        }

        return $this->render('fiche/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="delete_action", requirements={"id"="\d+"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $ficheSup = $em->getRepository(Fiche::class)->find($id);

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em->remove($ficheSup);
            $em->flush();

            $fiches = $em->getRepository(Fiche::class)->findAll();

            return $this->redirectToRoute('fiche_acceuil', ['fiches' => $fiches]);
        }

        return $this->render('fiche/delete.html.twig', ['formData' => $ficheSup]);
    }
}
