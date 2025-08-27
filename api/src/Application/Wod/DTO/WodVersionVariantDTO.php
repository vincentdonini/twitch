<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use App\Application\Exercise\DTO\ExerciseDTO;
use App\Domain\Wod\Enum\Gender;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

class WodVersionVariantDTO extends BaseDTO
{
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public Gender $gender;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public ?int $rounds;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public ?int $timeCap;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public string $description;

    #[Groups(['wod:detail'])]
    public int $wodVersionId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public array $relationships = [];

    public function __construct(
        int     $id,
        Gender  $gender,
        ?int    $rounds,
        ?int    $timeCap,
        ?string $description,
        int     $wodVersionId
    ) {
        parent::__construct($id);

        $this->gender       = $gender;
        $this->rounds       = $rounds;
        $this->timeCap      = $timeCap;
        $this->description  = $description;
        $this->wodVersionId = $wodVersionId;

        $this->relationships = [
            'wodVersion' => ['type' => 'wodVersions', 'id' => $wodVersionId],
        ];
    }
}
