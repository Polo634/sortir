<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"mail"}, message="Cet email est déja utilisé")
 * @UniqueEntity(fields={"pseudo"}, message="ce pseudo est déjà utilisé")
 */
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message = "L'email '{{ mail }}' n'est pas valide.") //regex
     */
    private $mail;


    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $motPasse;


    /**
     * @var string The hashed password
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(min=6, minMessage="Votre mot de passe doit faire au moins 6 caractères !")
     */
    private $newPassword;


    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank(message="Merci d'entrer votre nom")
     * @Assert\Length(min=2, max=80, minMessage="Merci d'entrer au minimum 2 caractères",
     *     maxMessage="Le nombre de caractères est limité à 80")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank(message="Merci d'entrer votre prénom")
     * @Assert\Length(min=2, max=80, minMessage="Merci d'entrer au minimum 2 caractères",
     *     maxMessage="Le nombre de caractères est limité à 80")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoProfil;


    //Cette propriété ne sert qu'à recevoir l'objet créé par Symfony lors de l'upload du fichier
    //Et à faire la validation
    /**
     *
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 2000,minWidthMessage="la largeur minimale autorisée est de 200 pixels",
     * maxWidthMessage="la largeur maximale autorisée est de 2000 pixels",)
     *     (minHeight = 200,
     *     maxHeight = 2000,maxHeightMessage="la hauteur maximale autorisée est de 2000 pixels",
     * minHeightMessage="la hauteur minimale autorisée est de 200 pixels")
     *     (maxSize = "20M",
     * maxSizeMessage="Attention la photo ne doit pas dépasser 20MO")
     *)
     */
    private $modifPhoto;

    /**
     * @ORM\Column(type="string",length=20 , nullable=true)
     */
    private ?string $telephone;


    /**
     * @ORM\Column(type="string", length=80, unique=true)//regex
     */
    private $pseudo;


    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, inversedBy="participants")
     */
    private $inscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesOrganisees;


    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->mail;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->mail;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->motPasse;
    }

    public function getMotPasse(): string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): self
    {
        $this->motPasse = $motPasse;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getModifPhoto(): ?UploadedFile
    {
        return $this->modifPhoto;
    }

    /**
     * @param UploadedFile $modifPhoto
     */
    public function setModifPhoto(?UploadedFile $modifPhoto): void
    {
        $this->modifPhoto = $modifPhoto;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }


    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }


    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Sortie $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
        }

        return $this;
    }

    public function removeInscription(Sortie $inscription): self
    {
        $this->inscriptions->removeElement($inscription);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees[] = $sortiesOrganisee;
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->administrateur ? ['ROLE_ADMIN'] : ['ROLE_USER'];
    }

}
