<?php

namespace App\Entity;

enum Status: string
{
    case Draft = "DRAFT";
    case PendingModerated = "PENDING_MODERATED";
    case Published = "PUBLISHED";
}