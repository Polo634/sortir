<?php

namespace App\Models;



use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Component\Validator\Constraints\Date;

class Filtre
{
    /**
     * @var string | null
     */
    public ?string $q = null;


    public ?Campus $campus;

    /**
     * @var \DateTime | null
     */
    public ?\DateTime $firstDate = null;

    /**
     * @var \DateTime | null
     */
    public ?\DateTime $lastDate = null;


    public bool $organisateur = false;

    public bool $inscrit = false;

    public bool $pasInscrit = false;


    public bool $sortiesPassees = false;


}
