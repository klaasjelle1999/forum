<?php


namespace App\FormData;

use Symfony\Component\Validator\Constraints as Assert;

class EditPasswordFormData
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $password;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    public $new_password;
}
