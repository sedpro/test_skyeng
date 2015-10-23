<?php

namespace Application\Entity;

class TeacherPupil extends Simple implements EntityInterface
{
    protected $fields = [
        'id',
        'teacher_id',
        'pupil_id',
    ];

    public $id;
    public $teacher_id;
    public $pupil_id;
}
