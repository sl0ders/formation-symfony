<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention , la saisie doit etre de type date xx/xx/xxxx")
     * @Assert\GreaterThan("today", message="la date de depart doit etre ulterieur a la date actuel", groups={"front"})
     */
    private $startTime;

    /**
     * @Assert\Date(message="Attention , la saisie doit etre de type date xx/xx/xxxx")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * Callback appelé a chaque fois qu'on crée une reservation)
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @throws Exception
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        if (empty($this->amount)) {
            // prix de l'annonce * nombre de jour
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function isBookableDates()
    {
        // 1) Il faut connaitre les dates qui sont impossibles pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // 2) Il faut comparer les dates choisies avec les dates impossibles
        $bookingDays    = $this->getDays();

        $formatDay = function ($day) {
            return $day->format('Y-m-d');
        };

        $days           = array_map($formatDay, $bookingDays);

        $notAvailable   = array_map($formatDay, $notAvailableDays);

        foreach ($days as $day) {
            if (array_search($day, $notAvailable) !== false) return false;
        }

        return true;
    }

    /**
     * Permet de récuperer un tableau des journées qui correspondent a ma reservation
     *
     * @return array Un tableau d'objets DateTime representant les jours de la réservation
     */
    public function getDays()
    {
        $resultat = range(
            $this->startTime->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function ($dayTimestamp) {
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat);
        return $days;
    }

    public function getDuration()
    {
        $diff = $this->endDate->diff($this->startTime);
        return $diff->days;
    }

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(propertyPath="startTime", message="la date d'arrivée doit etre ulterieur a la date de depart")
     */
    private $endDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
