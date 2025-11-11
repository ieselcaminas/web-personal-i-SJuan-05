<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, RedirectResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name:'users_index')]
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();
        return $this->render('users/index.html.twig', ['users' => $users]);
    }

    #[Route('/{id}', name:'users_show', methods:['GET'])]
    public function show(User $user): Response
    {
        return $this->render('users/show.html.twig', ['user' => $user]);
    }

    #[Route('/{id}/edit', name:'users_edit', methods:['GET','POST'])]
    public function edit(Request $request, User $user): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Debes iniciar sesión.');
        }

        if ($currentUser->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('No puedes editar otro perfil.');
        }

        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));
            $this->em->flush();

            $this->addFlash('success', 'Perfil actualizado correctamente.');
            return $this->redirectToRoute('users_show', ['id' => $user->getId()]);
        }

        return $this->render('users/edit.html.twig', ['user' => $user]);
}

}
