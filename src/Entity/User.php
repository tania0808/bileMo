<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;
use JMS\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @Hateoas\Relation(
 *     "list",
 *     href = @Hateoas\Route(
 *       "client_users_list",
 *         parameters = { "id" = "expr(object.getClient().getId())", "page" = "expr(1)", "limit" = "expr(10)" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = {"index"}, excludeIf = "expr(not is_granted('ROLE_USER'))")
 * )
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "client_user_detail",
 *         parameters = { "client" = "expr(object.getClient().getId())", "user" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = {"index"}, excludeIf = "expr(not is_granted('ROLE_USER'))")
 * )
 *
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route(
 *         "client_user_create",
 *         parameters = { "id" = "expr(object.getClient().getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = {"index"}, excludeIf = "expr(not is_granted('ROLE_USER'))")
 * )
 *
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *         "client_user_delete",
 *         parameters = { "client" = "expr(object.getClient().getId())", "user" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = {"index"}, excludeIf = "expr(not is_granted('ROLE_USER'))")
 * )
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    use Timestampable;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['index'])]
    #[Assert\NotBlank(message: 'Please enter a first name')]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['index'])]
    #[Assert\NotBlank(message: 'Please enter a last name')]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['index'])]
    #[Assert\NotNull(message: 'Please enter an email')]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['index'])]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['index'])]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['index'])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->created_at = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
