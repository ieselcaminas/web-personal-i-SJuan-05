<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\{PasswordAuthenticatedUserInterface, UserInterface};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id = null;

    #[ORM\Column(type:'string', length:255)]
    private string $name;

    #[ORM\Column(type:'string', length:180, unique:true)]
    private string $email;

    #[ORM\Column(type:'string')]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class, cascade: ['remove'])]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    // UserInterface / PasswordAuthenticatedUserInterface
    public function getRoles(): array { return ['ROLE_USER']; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getSalt(): ?string { return null; }
    public function getUserIdentifier(): string { return $this->email; }
    public function eraseCredentials(): void {}

    /** @return Collection|Post[] */
    public function getPosts(): Collection { return $this->posts; }
}
