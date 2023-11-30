<?php

namespace App\Dto;

use App\Entity\Status;

class UpdatePostStatusDto
{
    private Status $status;

    static function of(Status $status): UpdatePostStatusDto
    {
        $dto = new UpdatePostStatusDto();
        $dto->setStatus($status);

        return $dto;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     * @return UpdatePostStatusDto
     */
    public function setStatus(Status $status): UpdatePostStatusDto
    {
        $this->status = $status;
        return $this;
    }


}