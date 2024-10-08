<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}
    // Route par la page de connection
    #[Route('/register', name: 'register')]
    public function register(

        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager

    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Encode le mot de passe en utilisant plainPassword Algorythme Bcrypt 
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
            // set roles
            $user->setRoles($form->get('roles')->getData());

            $entityManager->persist($user);
            $entityManager->flush();


            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('admin@fiverrjr.com', 'Admin Site'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmeé votre Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $this->addFlash('success', 'Félicitations ! Votre inscription a été enregistrée avec succès. Un e-mail de confirmation a été envoyé.
r');
            // do anything else you need here, like send an email
            return $security->login($user, AppAuthenticator::class, 'main');
        }
        return $this->render('registration/register.html.twig', [
            'title_page' => 'Enregistrement',
            'registrationForm' => $form->createView()
        ]);
    }

    // Traitement de l'Email de comfirmation d'inscription
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse e-mail a été vérifiée');

        return $this->redirectToRoute('login');
    }
}
