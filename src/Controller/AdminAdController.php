<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Comment;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     * @param AdRepository $repo
     * @param $page
     * @param Pagination $pagination
     * @return Response
     */
    public function index(AdRepository $repo, $page, Pagination $pagination)
    {
        $pagination
            ->setEntityClass(Ad::class)
            ->setPage($page);
        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * Permet d'afficher le formulaire d'édition d'une annonce
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong> à bien etait enregistrée"
            );
        }
        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete",name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return RedirectResponse
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong>car elle possede déja des révervation"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
            );
        }
        return $this->redirectToRoute('admin_ads_index');
    }

}
