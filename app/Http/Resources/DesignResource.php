<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{

    public function toArray($request)
    {
        return [

            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'images'      => $this->images,
            'is_live'     => $this->is_live,
            'likes_count' => $this->likes()->count(),
            'description' => $this->description,
            'tag_list'    => [
                'tags'       =>$this->tagArray,
                'normalized' =>$this->tagArrayNormalized
            ],
            'created_at_dates' => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_at'       => $this->created_at
            ],
            'updated_at_dates' => [
                'updated_at_human' => $this->updated_at->diffForHumans(),
                'updated_at'       => $this->updated_at
            ],
            'comments' => CommentResource::collection(
                            $this->whenLoaded('comments')),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}