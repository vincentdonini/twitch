<?php

namespace App\Domain\Wod\Wod;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\Domain\Wod\Ports\WodDALInterface;
use App\Domain\Wod\Ports\WodTypeDALInterface;
use App\Infrastructure\Doctrine\Repository\Wod\WodRepository;

final readonly class UpdateWodUseCase
{
    public function __construct(
        private DatabaseInterface       $database,
        private WodDALInterface         $wodDAL,
        private WodTypeDALInterface     $wodTypeDAL,
        private WodCategoryDALInterface $wodCategoryDAL,
    ) {
    }

    public function execute(UpdateWodDTOInterface $dto): Wod
    {
        $wod = $this->wodDAL->getById($dto->getId());
        if(!$wod instanceof Wod){
            throw new EntityNotFoundException();
        }

        if(!empty($dto->getName())){
            if(!$this->validateDuplicate($dto->getName())){
                throw new AlreadyExistException();
            }

            $wod->setName($dto->getName());
        }

        if(!empty($dto->getTypeId())){
            $wodType = $this->wodTypeDAL->getById($dto->getTypeId());
            $wod->setWodType($wodType);
        }

        if(!empty($dto->getCategoryId())){
            $wodCategory = $this->wodCategoryDAL->getById($dto->getCategoryId());
            $wod->setWodCategory($wodCategory);
        }

        $this->database->preSave($wod);
        $this->database->save();

        return $wod;
    }

    private function validateDuplicate(string $name): bool
    {
        return !$this->wodDAL->getByName($name);
    }
}
