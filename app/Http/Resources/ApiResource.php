<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    protected $success;
    protected $message;
    
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  bool  $success
     * @param  string  $message
     * @return void
     */
    public function __construct($resource, $success = true, $message = '')
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->resource
        ];
    }
}