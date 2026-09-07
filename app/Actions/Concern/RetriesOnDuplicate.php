<?php

namespace App\Actions\Concern;

use Illuminate\Database\QueryException;

trait RetriesOnDuplicate
{
    private function retryOnDuplicate(callable $callback, int $maxRetries = 3)
    {
        $attempts = 0;
        while (true) {
            try {
                return $callback();
            } catch (QueryException $e) {
                if ($e->getCode() !== '23000' || ++$attempts >= $maxRetries) {
                    throw $e;
                }
                usleep(random_int(10_000, 100_000));
            }
        }
    }
}
