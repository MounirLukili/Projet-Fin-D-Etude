<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
            return $this->redirectToRoute($this->getTargetPathBasedOnRole());

        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    private function getTargetPathBasedOnRole(): string
{
    // Get the role of the user directly as a string
    $role = $this->getUser()->getRole();  // Assuming the method getRole() returns the role directly as a string

    // Check for admin role
    if ($role === 'ROLE_ADMIN') {
        return 'admin_dashboard'; // Route name for admin dashboard
    }

    // Check for teacher role
    if ($role === 'ROLE_TEACHER') {
        return 'teacher_dashboard'; // Route name for teacher dashboard
    }

    // Check for student role
    if ($role === 'ROLE_STUDENT') {
        return 'student_dashboard'; // Route name for student dashboard
    }

    // No roles matched, throw an exception
    throw new \LogicException('No valid role found for redirection');
}





    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
