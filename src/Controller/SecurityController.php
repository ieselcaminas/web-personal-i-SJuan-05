<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('posts_index');
        }
        return $this->render('auth/login.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('posts_index');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirmation');

            if (!$name || !$email || !$password || $password !== $passwordConfirm) {
                $this->addFlash('danger', 'Datos inválidos o contraseñas no coinciden.');
            } else {
                $user = new User();
                $user->setName($name)
                     ->setEmail($email)
                     ->setPassword($hasher->hashPassword($user, $password));

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Cuenta creada correctamente.');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('auth/register.html.twig');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('Logout manejado por Symfony.');
    }
}
