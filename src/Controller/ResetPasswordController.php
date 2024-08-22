<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        // Interface utilisée pour générer et valider les tokens de réinitialisation de mot de passe.
        private ResetPasswordHelperInterface $resetPasswordHelper,
        // Interface pour gérer les entités dans la base de données.
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     * 
     * Gère la demande initiale de réinitialisation 
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
                $translator
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     * 
     * Affiche une page de confirmation après que l'utilisateur a demandé la réinitialisation de mot de passe
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(TranslatorInterface $translator): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not

        // Génère un token factice si aucun token valide n'est trouvé.
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     * 
     * Gère la validation et le processus de réinitialisation du mot de passe
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, ?string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            // Nous stockons le token en session et le supprimons de l'URL
            // pour éviter que l'URL ne soit chargé dans un navigateur et potentiellement divulgué le jeton à du JavaScript tiers
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }
        // On recherche le token en session
        $token = $this->getTokenFromSession();
        // S'il est null on s'oulève une exception
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            // Valide le token et récupère l'utilisateur correspondant.
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } 
        
        catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        // Crée un formulaire pour permettre  de saisir un nouveau mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // A password reset token should be used only once, remove it.
            // Un jeton de réinitialisation de mot de passe ne doit être utilisé qu’une seule fois, supprimez-le.
            $this->resetPasswordHelper->removeResetRequest($token);
            // Encode(hash) the plain password, and set it.
            // encode le nouveau mot de passe, 
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($encodedPassword);
            // met à jour l'utilisateur en base de données et 
            $this->entityManager->flush();
            // The session is cleaned up after the password has been changed.
            // Nettoye après la modification du mot de passe.
            $this->cleanSessionAfterReset();
            // redirige vers la page d'accueil
            return $this->redirectToRoute('home');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }
    // envoie l'email de réinitialisation de mot de passe :
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): RedirectResponse
    {
        // Recherche l'utilisateur par son email dans la base de données
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        // Ne pas révéler si un compte utilisateur a été trouvé ou non
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            // Génère un token de réinitialisation de mot de passe
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        // Crée et envoie un email avec le token.
        $email = (new TemplatedEmail())
            ->from(new Address('mailer@fiverrjr.com', 'Fiverr Junior'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;
        // Envoie de l'Email
        $mailer->send($email);
        // Store the token object in session for retrieval in check-email route.
        // Stocke le token dans la session pour récupération ultérieure.
        $this->setTokenObjectInSession($resetToken);
        // Redirige vers la page de confirmation
        return $this->redirectToRoute('app_check_email');
    }
}
