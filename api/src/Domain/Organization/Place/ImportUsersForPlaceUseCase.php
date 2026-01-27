<?php

namespace App\Domain\Organization\Place;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class ImportUsersForPlaceUseCase
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserDALInterface            $userDAL,
        private PlaceDALInterface           $placeDAL,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function execute(ImportUsersForPlaceDTOInterface $dto): ImportUsersForPlaceResult
    {
        $place = $this->placeDAL->getById($dto->getId());
        if (!$place instanceof Place) {
            throw new EntityNotFoundException("Place not found");
        }

        $created  = 0;
        $existing = 0;
        $attached = 0;
        $errors   = [];

        if (!is_file($dto->getCsvPath())) {
            throw new InvalidPayloadException("CSV file does not exist");
        }

        $handle = fopen($dto->getCsvPath(), 'r');
        if (!$handle) {
            throw new InvalidPayloadException("Unable to read CSV");
        }

        $header = fgetcsv($handle, 0, ';');
        if (!$header) {
            fclose($handle);
            throw new InvalidPayloadException("CSV header is missing or invalid");
        }

        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $data = array_combine($header, $row);

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email: {$data['email']}";
                    continue;
                }

                try {
                    $user = $this->userDAL->findOneBy(['email' => $data['email']]);

                    if ($user) {
                        $existing++;
                    } else {
                        $user = new User(
                            email    : $data['email'],
                            firstName: $data['firstName'],
                            lastName : $data['lastName'],
                        );

                        $hashedPassword = $this->passwordHasher->hashPassword($user, 'ChangeMe123!');
                        $user->setPassword($hashedPassword);

                        $this->entityManager->persist($user);
                        $created++;
                    }

                    $place->addUser($user);
                    $attached++;

                } catch (\Throwable $e) {
                    $errors[] = sprintf(
                        'Error processing email "%s": %s',
                        $data['email'] ?? 'unknown',
                        $e->getMessage()
                    );
                }
            }

            $this->entityManager->flush();
        } finally {
            fclose($handle);
        }

        return new ImportUsersForPlaceResult(
            created : $created,
            existing: $existing,
            attached: $attached,
            errors  : $errors
        );
    }
}
