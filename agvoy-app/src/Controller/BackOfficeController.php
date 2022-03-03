<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Owner;
use App\Entity\Room;
use App\Form\RegionType;
use App\Form\OwnerType;
use App\Form\RoomType;
use App\Repository\RegionRepository;
use App\Repository\OwnerRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/backoffice")
 * @IsGranted("ROLE_ADMIN")
 */
class BackOfficeController extends AbstractController
{
    /**
     * @Route("/region/list", name="region_index", methods={"GET"})
     */
    public function region_index(RegionRepository $regionRepository): Response
    {
        return $this->render('backoffice/index.html.twig', [
            'regions' => $regionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/region/new", name="region_new", methods={"GET","POST"})
     */
    public function region_new(Request $request): Response
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
            $entityManager->flush();

            return $this->redirectToRoute('region_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/new.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/region/{id}", name="region_show", methods={"GET"})
     */
    public function region_show(Region $region): Response
    {
        return $this->render('backoffice/show.html.twig', [
            'region' => $region,
        ]);
    }

    /**
     * @Route("/region/{id}/edit", name="region_edit", methods={"GET","POST"})
     */
    public function region_edit(Request $request, Region $region): Response
    {
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('region_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/edit.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/region/{id}", name="region_delete", methods={"POST"})
     */
    public function region_delete(Request $request, Region $region): Response
    {
        if ($this->isCsrfTokenValid('delete'.$region->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($region);
            $entityManager->flush();
        }

        return $this->redirectToRoute('region_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/owner", name="backoffice_owner_index", methods={"GET"})
     */
    public function owner_index(OwnerRepository $ownerRepository): Response
    {
        return $this->render('backoffice_owner/index.html.twig', [
            'owners' => $ownerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/owner/new", name="backoffice_owner_new", methods={"GET","POST"})
     */
    public function owner_new(Request $request): Response
    {
        $owner = new Owner();
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($owner);
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_owner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice_owner/new.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/{id}", name="backoffice_owner_show", methods={"GET"})
     */
    public function owner_show(Owner $owner): Response
    {
        return $this->render('backoffice_owner/show.html.twig', [
            'owner' => $owner,
        ]);
    }

    /**
     * @Route("/owner/{id}/edit", name="backoffice_owner_edit", methods={"GET","POST"})
     */
    public function owner_edit(Request $request, Owner $owner): Response
    {
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_owner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice_owner/edit.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/{id}", name="backoffice_owner_delete", methods={"POST"})
     */
    public function owner_delete(Request $request, Owner $owner): Response
    {
        if ($this->isCsrfTokenValid('delete'.$owner->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($owner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_owner_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/room/", name="backoffice_room_index", methods={"GET"})
     */
    public function room_index(RoomRepository $roomRepository): Response
    {
        return $this->render('backoffice_room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    /**
     * @Route("/room/new", name="backoffice_room_new", methods={"GET","POST"})
     */
    public function room_new(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice_room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/room/{id}", name="backoffice_room_show", methods={"GET"})
     */
    public function room_show(Room $room): Response
    {
        return $this->render('backoffice_room/show.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/room/{id}/edit", name="backoffice_room_edit", methods={"GET","POST"})
     */
    public function room_edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice_room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/room/{id}", name="backoffice_room_delete", methods={"POST"})
     */
    public function room_delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_room_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/user/", name="backoffice_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backoffice_user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/user/new", name="backoffice_user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $userPasswordEncoderInterface): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoderInterface->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('backoffice_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice_user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}", name="backoffice_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $plain_password = $user->getPassword();

        return $this->render('backoffice_user/show.html.twig', [
            'user' => $user,
            'plain_password' => $plain_password
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="backoffice_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $userPasswordEncoderInterface): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoderInterface->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice_user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}", name="backoffice_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
