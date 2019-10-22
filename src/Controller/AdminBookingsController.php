<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingsController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings")
     * @param BookingRepository $repository
     * @return Response
     */
    public function index(BookingRepository $repository)
    {
        $bookings = $repository->findAll();
        return $this->render('admin/bookings/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/edit",name="admin_bookings_edit")
     * @param Booking $booking
     * @param ObjectManager $manager
     * @param Request $request
     * @return Response
     */
    public function edit(Booking $booking, ObjectManager $manager, Request $request)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La reservation n°{$booking->getId()} a bien été modifiée"
            );
            return $this->redirectToRoute("admin_bookings");
        }


        return $this->render('admin/bookings/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/delete",name="admin_booking_delete")
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return RedirectResponse
     */
    public function delete(Booking $booking, ObjectManager $manager){
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La reservation a bien été supprimée"
        );
        return $this->redirectToRoute("admin_bookings");
    }
}
