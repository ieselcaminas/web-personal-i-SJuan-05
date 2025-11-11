<?php
namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comentario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, RedirectResponse, File\UploadedFile};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/posts')]
class PostController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name:'posts_index', methods:['GET'])]
    public function index(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findBy([], ['createdAt'=>'DESC']);
        return $this->render('posts/index.html.twig', ['posts' => $posts]);
    }

    #[Route('/mis-posts', name:'posts_mis', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function myPosts(): Response
    {
        $user = $this->getUser();
        $posts = $this->em->getRepository(Post::class)->findBy(['user' => $user], ['createdAt' => 'DESC']);
        return $this->render('posts/show.html.twig', ['posts' => $posts]);
    }

    #[Route('/create', name:'posts_create', methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $titulo = $request->request->get('titulo');
            $contenido = $request->request->get('contenido');
            /** @var UploadedFile $imagen */
            $imagen = $request->files->get('imagen');

            if (!$titulo || !$contenido) {
                $this->addFlash('danger', 'Título y contenido son obligatorios.');
            } else {
                $post = new Post();
                $post->setTitulo($titulo)->setContenido($contenido)->setUser($this->getUser());
                if ($imagen) {
                    $uploadsDir = $this->getParameter('uploads_directory');
                    $filename = uniqid().'.'.$imagen->guessExtension();
                    $imagen->move($uploadsDir, $filename);
                    $post->setImagen('uploads/'.$filename);
                }
                $this->em->persist($post);
                $this->em->flush();
                $this->addFlash('success', 'Post creado exitosamente.');
                return $this->redirectToRoute('posts_index');
            }
        }
        return $this->render('posts/create.html.twig');
    }

    #[Route('/{id}/edit', name:'posts_edit', methods:['GET','POST'])]
    public function edit(Request $request, Post $post): Response
    {
        // Autorización: solo owner
        
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Debes iniciar sesión.');
        }

        if ($user->getId() !== $post->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }


        if ($request->isMethod('POST')) {
            $post->setTitulo($request->request->get('titulo'));
            $post->setContenido($request->request->get('contenido'));
            $imagen = $request->files->get('imagen');
            if ($imagen) {
                $uploadsDir = $this->getParameter('uploads_directory');
                $filename = uniqid().'.'.$imagen->guessExtension();
                $imagen->move($uploadsDir, $filename);
                $post->setImagen('uploads/'.$filename);
            }
            $this->em->flush();
            $this->addFlash('success', 'Post actualizado correctamente.');
            return $this->redirectToRoute('posts_mis');
        }

        return $this->render('posts/edit.html.twig', ['post' => $post, 'user' => $post->getUser()]);
    }

    #[Route('/{id}/delete', name:'posts_delete', methods:['POST'])]
    public function delete(Request $request, Post $post): RedirectResponse
    {
        
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Debes iniciar sesión.');
        }

        if ($user->getId() !== $post->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $this->em->remove($post);
        $this->em->flush();
        $this->addFlash('success', 'Post eliminado correctamente.');
        return $this->redirectToRoute('posts_index');
    }

    #[Route('/{id}', name:'posts_comments', methods:['GET'])]
    public function commentsIndex(Post $post): Response
    {
        $comments = $this->em->getRepository(Comentario::class)->findBy(['post' => $post], ['createdAt'=>'DESC']);
        return $this->render('comentarios/index.html.twig', ['post' => $post, 'comments' => $comments]);
    }

    #[Route('/{id}/comments', name:'posts_comments_store', methods:['POST'])]
    public function commentsStore(Request $request, Post $post): RedirectResponse
    {
        $user = $this->getUser();

        // Verifica que haya un usuario autenticado
        if (!$user instanceof \App\Entity\User) {
            $this->addFlash('warning', 'Debes iniciar sesión para comentar.');
            return $this->redirectToRoute('login');
        }

        $cuerpo = $request->request->get('cuerpo');
        $autor = $user->getName(); // ✅ Ahora no marcará error

        if (!$cuerpo) {
            $this->addFlash('danger', 'El comentario está vacío.');
        return $this->redirectToRoute('posts_comments', ['id' => $post->getId()]);
        }

        $comentario = new Comentario();
        $comentario->setPost($post)
               ->setAutor($autor)
               ->setCuerpo($cuerpo);

        $this->em->persist($comentario);
        $this->em->flush();

        $this->addFlash('success', 'Comentario añadido correctamente.');
        return $this->redirectToRoute('posts_comments', ['id' => $post->getId()]);
    }


    // API endpoint simple para listar posts en JSON
    #[Route('/api/posts', name:'api_posts', methods:['GET'])]
    public function apiPosts(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        $data = array_map(function(Post $p) {
            return [
                'id' => $p->getId(),
                'titulo' => $p->getTitulo(),
                'contenido' => $p->getContenido(),
                'imagen' => $p->getImagen(),
                'user' => $p->getUser()->getName(),
                'createdAt' => $p->getCreatedAt()->format('c'),
            ];
        }, $posts);
        return $this->json($data);
    }
}
