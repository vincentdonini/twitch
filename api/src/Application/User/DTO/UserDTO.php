<?php

namespace App\Application\User\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class UserDTO
{
    #[Groups([
        'user:list', 'user:detail',
    ])]
    public int $id;

    #[Groups([
        'user:list', 'user:detail',
    ])]
    public string $email;

    #[Groups([
        'user:list', 'user:detail',
    ])]
    public string $firstName;

    #[Groups([
        'user:list', 'user:detail',
    ])]
    public string $lastName;

    public function __construct(
        int    $id,
        string $email,
        string $firstName,
        string $lastName,
    ) {
        $this->id        = $id;
        $this->email     = $email;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }
}
