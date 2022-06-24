<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email]
    private $email;
    
    #[ORM\Column(type: 'string', length: 255)]
    private $prenom;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string')]
    private $password;

    private $newPassword;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'responsable')]
    #[ORM\JoinTable(name:'equipe')]
    private $demandeurs;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'demandeurs')]
    private $responsable;


    public function __construct()
    {
        $this->demandeurs = new ArrayCollection();
        $this->responsable = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getFullName(): string 
    {
        return $this->getPrenom().' '.$this->getNom();
    }
    

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_DEMANDEUR';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isResponsable(): bool {
        if(in_array('ROLE_RESPONSABLE',$this->roles)) {
            return true;
        }
        return false;
    }

    public function isRh(): bool {
        if(in_array('ROLE_RH',$this->roles)) {
            return true;
        }
        return false;
    }

    public function isAdmin(): bool {
        if(in_array('ROLE_ADMIN',$this->roles)) {
            return true;
        }
        return false;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }


    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDemandeurs(): Collection
    {
        return $this->demandeurs;
    }

    public function addDemandeurs(self $demandeurs): self
    {
        if (!$this->demandeurs->contains($demandeurs)) {
            $this->demandeurs[] = $demandeurs;
        }

        return $this;
    }

    public function removeDemandeurs(self $demandeurs): self
    {
        $this->demandeurs->removeElement($demandeurs);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getResponsable(): Collection
    {
        return $this->responsable;
    }

    public function addResponsable(self $responsable): self
    {
        if (!$this->responsable->contains($responsable)) {
            $this->responsable[] = $responsable;
            $responsable->addDemandeurs($this);
        }

        return $this;
    }

    public function removeResponsable (self $responsable): self
    {
        if ($this->responsable->removeElement($responsable)) {
            $responsable->removeDemandeurs($this);
        }

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    // retourne un tableau avec tous les demandeurs d'un responsable, avec lui-même en propre position
    public function getDemandeurFilter(User $user): array
    {
        $result = [];
        if($this->demandeurs != null) {
            $result['Moi'] = $user;
            foreach($this->demandeurs as $demandeur) {
                if($demandeur != $user) {
                    $result[$demandeur->getFullName()] = $demandeur;
                }
            }
        }
        return $result;
    }


}
