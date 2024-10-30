<?php

namespace App\Controller;

use App\Provider\MicrosoftProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\MicrosoftAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{

    public function __construct(
        private string                $microsoftClientId,
        private string                $microsoftClientSecret,
        private string                $microsoftCallbackUrl,
        private MicrosoftAuthService  $microsoftAuthService,
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    #[Route(path: '/', name: 'app_microsoft_login')]
    public function microsoftLogin(): Response
    {
        $provider = new MicrosoftProvider([
            'clientId' => $this->microsoftClientId,
            'clientSecret' => $this->microsoftClientSecret,
            'redirectUri' => $this->microsoftCallbackUrl,
        ]);

        $authUrl = $provider->getAuthorizationUrl();
        return new RedirectResponse($authUrl);
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: 'microsoft/callback', name: 'app_microsoft_callback')]
    public function microsoftCallBack(Request $request, TranslatorInterface $translator): Response
    {

        $code = $request->get('code');

        $userData = $this->microsoftAuthService->getLoggedInUser($code);
        $result = $this->microsoftAuthService->addUserToGroupByIds($userData['id']);

        if ($result) {
            $status = 'success';
            $message = $translator->trans('microsoft.login-by-microsoft.success');
        } else {
            $status = 'error';
            $message = $translator->trans('microsoft.login-by-microsoft.error');
        }


        $this->addFlash($status, $message);
        return new RedirectResponse($this->urlGenerator->generate('app_microsoft_success_login'));
    }


    #[Route(path: '/microsoft-success-login', name: 'app_microsoft_success_login')]
    public function successLogin(): Response
    {
        return $this->render('security/microsoft/success_login.html.twig');
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
