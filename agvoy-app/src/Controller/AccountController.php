<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Entity\Room;
use App\Form\AccountType;
use App\Form\UserAccountType;
use App\Form\OwnerType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/account")
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{

    /**
     * @Route("/{id}", name="account_show", requirements={ "id": "\d+"}, methods={"GET"})
     * @IsGranted("ROLE_OWNER")
     */
    public function owner_show(User $user): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('account/show.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route("/{id}/edit/owner", name="owner_account_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_OWNER")
     */
    public function owner_edit(Request $request, User $user): Response
    {
        $owner=$user->getOwner();
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //Add a flash message
            $this->get('session')->getFlashBag()->add('message', 'Votre compte a bien été modifié');

            return $this->redirectToRoute('account_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit/user", name="account_edit", requirements={ "id": "\d+"}, methods={"GET","POST"})
     */
    public function owner_account_edit(Request $request, User $user, UserPasswordEncoderInterface $userPasswordEncoderInterface): Response
    {
        $form = $this->createForm(UserAccountType::class, $user);
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

            //Add a flash message
            $this->get('session')->getFlashBag()->add('message', 'Votre compte a bien été modifié');

            return $this->redirectToRoute('account_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/user_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="account_delete", requirements={ "id": "\d+"}, methods={"POST"})
     * @IsGranted("ROLE_OWNER")
     */
    public function owner_delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user->getOwner());
            $entityManager->remove($user);
            $entityManager->flush();
        }
        
        //Add a flash message
        $this->get('session')->getFlashBag()->add('message', 'Votre compte a bien été supprimé');
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/user/{id}", name="user_account_show", requirements={ "id": "\d+"}, methods={"GET"})
     */
    public function user_show(User $user): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('account/user.show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_account_edit", requirements={ "id": "\d+"}, methods={"GET","POST"})
     */
    public function user_edit(Request $request, User $user, UserPasswordEncoderInterface $userPasswordEncoderInterface): Response
    {
        $form = $this->createForm(UserAccountType::class, $user);
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

            //Add a flash message
            $this->get('session')->getFlashBag()->add('message', 'Votre compte a bien été modifié');

            return $this->redirectToRoute('user_account_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="account_delete", requirements={ "id": "\d+"}, methods={"POST"})
     */
    public function user_delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        
        //Add a flash message
        $this->get('session')->getFlashBag()->add('message', 'Votre compte a bien été supprimé');
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/user/{id}/fav-rooms", name="faved_rooms", requirements={ "id": "\d+"}, methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function user_fav_rooms(User $user): Response
    {

        $em = $this->getDoctrine()->getManager();
        $rooms = $em->getRepository(Room::class)->findAll();

        dump($rooms);

        return $this->render('account/fav.room.html.twig', [
            'rooms' => $rooms ]
        );
        
    }

    
}
