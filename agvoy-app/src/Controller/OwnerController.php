<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Owner;
use App\Entity\Room;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Owner_RoomType;
use App\Form\OwnerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * Controleur Owner
 * @Route("/owner")
 * @IsGranted("ROLE_USER")
 */
class OwnerController extends AbstractController
{
    /**
     * @Route("/list", name="owner_index", methods="GET")
     */
    public function owner_index():Response
    {
        $em = $this->getDoctrine()->getManager();
        $owners = $em->getRepository(Owner::class)->findAll();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $your_owner=$user->getOwner();

        dump($owners);

        return $this->render('owner/index.html.twig', [
            'owners' => $owners,
            'your_owner' => $your_owner ]
        );
    }

    /**
     * Finds and displays an owner entity.
     *
     * @Route("/{id}", name="owner_show", requirements={ "id": "\d+"}, methods="GET")
     */
    public function owner_show(Owner $owner):Response
    {
        $em = $this->getDoctrine()->getManager();
        $owners = $em->getRepository(Owner::class)->findAll();
        $rooms=$owner->getRooms();

        dump($owners);

        return $this->render('owner/show.html.twig', 
        ['owner' => $owner , 'rooms' => $rooms]);
    }

    /**
     * Finds and displays an owner's rooms.
     *
     * @Route("/{id}/room/list", name="owner_room_index", requirements={ "id": "\d+"}, methods="GET")
     */
    public function owner_room_index(Owner $owner):Response
    {
        $em = $this->getDoctrine()->getManager();
        $owners = $em->getRepository(Owner::class)->findAll();
        $rooms=$owner->getRooms();

        dump($owners);

        return $this->render('owner/room.index.html.twig', 
        ['owner' => $owner , 'rooms' => $rooms]);
    }

    /**
     * Finds and displays an owner's specific room.
     *
     * @Route("/{id}/room/{id_room}", name="owner_room_show", requirements={ "id": "\d+", "id_room": "\d+"}, methods="GET")
     */
    public function owner_room_show(Owner $owner, $id_room):Response
    {
        dump($owner);
        dump($id_room);
        $em = $this->getDoctrine()->getManager();
        $owners = $em->getRepository(Owner::class)->findAll();

        $rooms=$owner->getRooms();
        foreach ($rooms as $r) {
            if ($r->getId() == $id_room) {
                $room = $r;
            }
        }
        
        return $this->render('owner/room.show.html.twig', 
        ['owner' => $owner , 'room' => $room]);
    }


    /**
     * @Route("/{id}/room/new", name="owner_room_new", requirements={ "id": "\d+"}, methods={"GET", "POST"})
     * 
     */
    public function new(Request $request, Owner $owner): Response
    {
        $room = new Room();
        $room->setOwner($owner);
        $form = $this->createForm(Owner_RoomType::class, $room);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();
            
            //Add a flash message
            $this->get('session')->getFlashBag()->add('message', 'Votre propriété a bien été créée');
            return $this->redirectToRoute("owner_show", ['id' => $owner->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/new.room.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
            'owner' => $owner
        ]);
    }

    /**
     * @Route("/{id}/room/{id_room}/edit", name="owner_room_edit", requirements={ "id": "\d+", "id_room": "\d+"}, methods={"GET", "POST"})
     * 
     */
    public function edit(Request $request, Owner $owner, $id_room): Response
    {
        $rooms=$owner->getRooms();
        foreach ($rooms as $r) {
            if ($r->getId() == $id_room) {
                $room = $r;
            }
        }
        $form = $this->createForm(Owner_RoomType::class, $room);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->get('session')->getFlashBag()->add('message', 'Votre propriété a bien été modifiée');

            return $this->redirectToRoute("owner_show", ['id' => $owner->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/edit.room.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
            'owner' => $owner
        ]);
    }

    /**
     * @Route("/{id}/room/{id_room}", name="owner_room_delete", requirements={ "id": "\d+", "id_room": "\d+"}, methods={"POST"})
     */
    public function delete(Request $request, Owner $owner, $id_room): Response
    {
        $rooms=$owner->getRooms();
        foreach ($rooms as $r) {
            if ($r->getId() == $id_room) {
                $room = $r;
            }
        }
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute("owner_show", ['id' => $owner->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/new", name="owner_new", methods={"GET","POST"})
     */
    public function owner_new(Request $request): Response
    {
        $owner = new Owner();
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $owner->setUser($user);
        $user->setOwner($owner);
        $new_roles=array_merge($user->getRoles(), array('ROLE_OWNER'));
        $user->setRoles($new_roles);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($owner);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('message', 'Votre pouvez désormais louer vos propriétés');

            return $this->redirectToRoute('owner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/new.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
        ]);
    }

 


    
}
