<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'posts')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable:false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type:'string', length:255)]
    private string $titulo;

    #[ORM\Column(type:'text')]
    private string $contenido;

    #[ORM\Column(type:'string', length:255, nullable:true)]
    private ?string $imagen = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comentario::class, cascade:['remove'])]
    private Collection $comentarios;

    #[ORM\Column(type:'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters y setters...
    public function getId(): ?int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getTitulo(): string { return $this->titulo; }
    public function setTitulo(string $t): self { $this->titulo = $t; return $this; }
    public function getContenido(): string { return $this->contenido; }
    public function setContenido(string $c): self { $this->contenido = $c; return $this; }
    public function getImagen(): ?string { return $this->imagen; }
    public function setImagen(?string $i): self { $this->imagen = $i; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getComentarios(): Collection { return $this->comentarios; }
}
