<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controleur Room
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/list", name="room_index", methods="GET")
     */
    public function room_index():Response
    {
        $em = $this->getDoctrine()->getManager();
        $rooms = $em->getRepository(Room::class)->findAll();

        dump($rooms);

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms ]
        );
    }
        
    /**
     * Finds and displays a room entity.
     *
     * @Route("/{id}", name="public_room_show", requirements={ "id": "\d+"}, methods="GET")
     */
    public function room_show(Room $room):Response
    {
        $em = $this->getDoctrine()->getManager();
        $rooms = $em->getRepository(Room::class)->findAll();

        dump($rooms);

        return $this->render('room/public.show.html.twig', 
        ['room' => $room ]);
    }
    
    /**
     * @Route("/new", name="room_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="room_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Add a room the the user's cart
     * 
     * @Route("/mark/{id}", name="cart_mark", requirements={ "id": "\d+"}, methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function markAction(Room $room): Response
    {
        $panier = $this->get('session')->get('panier');
        $id=$room->getId();

        if (! is_array($panier)) {
            $panier=array($id);
            $this->get('session')->set('panier', $panier);
            
        } else {
            //enlever la chambre du panier
            if (in_array($id, $panier)) {
                $panier = array_diff($panier, array($id));
                $this->get('session')->set('panier', $panier);
            } else {
                //ajouter la chambre au panier
                array_push($panier, $id);
                $this->get('session')->set('panier', $panier);
            }
        }

        dump($room);
        return $this->redirectToRoute('public_room_show', 
        ['id' => $room->getId()]);
    }
}
