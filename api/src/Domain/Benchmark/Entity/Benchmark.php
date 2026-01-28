<?php

namespace App\Domain\Benchmark\Entity;

use App\Domain\Benchmark\Enum\Type;
use App\Domain\Content\Entity\ContentBenchmark;
use App\Domain\Exercise\Entity\Exercise;
use App\Infrastructure\Doctrine\Repository\Benchmark\BenchmarkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BenchmarkRepository::class)]
#[ORM\Table(name: 'benchmark')]
class Benchmark
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Exercise::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Exercise $exercise;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', enumType: Type::class)]
    private Type $type;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $value = null;

    #[ORM\OneToMany(targetEntity: ContentBenchmark::class, mappedBy: "benchmark", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        Exercise $exercise,
        string   $slug,
        string   $name,
        Type     $type,
    ) {
        $this->exercise = $exercise;
        $this->slug     = $slug;
        $this->name     = $name;
        $this->type     = $type;
        $this->contents = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function setExercise(Exercise $exercise): self
    {
        $this->exercise = $exercise;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentBenchmark $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setBenchmark($this);
        }
        return $this;
    }

    public function removeContent(ContentBenchmark $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentBenchmark
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
