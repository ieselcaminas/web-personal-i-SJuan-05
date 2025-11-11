<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comentarios')]
class Comentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comentarios')]
    #[ORM\JoinColumn(nullable:false, onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\Column(type:'string', length:255)]
    private string $autor;

    #[ORM\Column(type:'text')]
    private string $cuerpo;

    #[ORM\Column(type:'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters/Setters...
    public function getId(): ?int { return $this->id; }
    public function getPost(): Post { return $this->post; }
    public function setPost(Post $post): self { $this->post = $post; return $this; }
    public function getAutor(): string { return $this->autor; }
    public function setAutor(string $a): self { $this->autor = $a; return $this; }
    public function getCuerpo(): string { return $this->cuerpo; }
    public function setCuerpo(string $c): self { $this->cuerpo = $c; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
