<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gerer le formulaire de connexion
     *
     * @Route("/login", name="account_login")
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de ce deconnecter
     *
     * @Route("/logout",name="account_logout")
     * @return void
     */
    public function logout()
    {
        //...rien
    }

    /**
     *  Permet d'afficher le formulaire d'inscription
     *
     * @Route("/register", name="account_register")
     *
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre compte a bien etait créé ! Vous pouvez maintenant vous connecter"
            );
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profile
     *
     * @Route("/account/profile", name="account_profile")
     * @param Request $request
     * @param ObjectManager $manager
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success',
                "les données du profile on bien été modifié");
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param ObjectManager $manager
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager)
    {
        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //1. Verifier que le oldpassword de l'utilisateur soit bien le meme que le mot de passe actuel soit identique
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //gérer l'erreur

                //Je vais chercher le oldPassword pour intergir avec lui
                $oldPassword = $form->get('oldPassword');

                //ensuite je lui met un new FormError
                $oldPassword->addError(new FormError("
                    Le mot de passe que vous avez tapé n'est pas
                    le meme que votre mot de pass actuel"
                ));

            } else {
                //2. si le mot de passe actuel est bien le meme que celui rentré

                //2.1 j'identifie le nouveau password dans la variable $newPassword
                $newPassword = $passwordUpdate->getNewPassword();
                //2.2 Je hash le nouveau password
                $hash = $encoder->encodePassword($user, $newPassword);
                //2.3 je modifie l'ancien mot de passe par le nouveau
                $user->setHash($hash);
                //2.4 Je lance le tout dans la bdd
                $manager->persist($user);
                $manager->flush();
                //2.5 et pour finir je renvoie un message pour confirmer a l'utilisateur que son mot de passe a bien été modifier
                $this->addFlash('success',
                    "Votre mot de passe a bien été modifié"
                );
                return $this->redirectToRoute('homepage');
            }


        }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user' => $this->getUser()
        ]);
    }
}
