<?php

namespace App\Domain\Wod\Wod;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\Domain\Wod\Ports\WodDALInterface;
use App\Domain\Wod\Ports\WodTypeDALInterface;

final readonly class CreateWodUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodDALInterface         $wodDAL,
        private WodTypeDALInterface     $wodTypeDAL,
        private WodCategoryDALInterface $wodCategoryDAL,
    ) {
    }

    public function execute(CreateWodDTOInterface $dto): Wod
    {
        if(!$this->validatePayload($dto)){
            throw new InvalidPayloadException();
        }

        if(!$this->validateDuplicate($dto->getName())){
            throw new AlreadyExistException();
        }

        $wodType     = $this->wodTypeDAL->getById($dto->getTypeId());
        $wodCategory = $this->wodCategoryDAL->getById($dto->getCategoryId());

        $wod = new Wod(
            name       : $dto->getName(),
            wodType    : $wodType,
            wodCategory: $wodCategory,
        );
        $wod->setTeamSize($dto->getTeamSize() ?? null);

        $this->database->preSave($wod);
        $this->database->save();

        return $wod;
    }

    private function validatePayload(CreateWodDTOInterface $dto): bool
    {
        if (
            !$dto->getName() ||
            !$dto->getTypeId() ||
            !$dto->getCategoryId() ||
            ($dto->getTeamSize() !== null && $dto->getTeamSize() <= 0) ||
            !$this->wodTypeDAL->getById($dto->getTypeId()) ||
            !$this->wodCategoryDAL->getById($dto->getCategoryId())
        ) {
            return false;
        }
        return true;
    }

    private function validateDuplicate(string $name): bool
    {
        return !$this->wodDAL->getByName($name);
    }
}
