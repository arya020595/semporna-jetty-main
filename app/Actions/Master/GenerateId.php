<?php

namespace App\Actions\Master;

use Illuminate\Database\Eloquent\Builder;

class GenerateId
{

    protected $padLength = 4;

    /**
     * Execute the action
     *
     * @param  array  $data
     * @return string
     */
    public function execute(Builder $query, $pad = '0', $prefix = '')
    {
        $object = $query->withTrashed()
            ->count();

        return $this->generateCode($object + 1, $pad, $prefix);
    }

    public function generateCode($code, $pad = '0', $prefix = '')
    {
        return $prefix . str_pad($code, $this->padLength, $pad, STR_PAD_LEFT);
    }

    public function setPadLength($padLength)
    {
        $this->padLength = $padLength;
        return $this;
    }
}
