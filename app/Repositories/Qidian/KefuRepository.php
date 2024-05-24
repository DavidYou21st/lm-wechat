<?php

namespace App\Repositories\Qidian;

use App\Repositories\BaseRepository;

/**
 * 企点客服
 */
class KefuRepository extends BaseRepository
{
    public function model()
    {
        return "App\\Models\\Qidian\\QidianKefu";
    }
}
