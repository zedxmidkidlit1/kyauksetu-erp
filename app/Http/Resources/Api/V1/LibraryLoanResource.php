<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'borrowed_at' => $this->borrowed_at?->toJSON(),
            'due_at' => $this->due_at?->toJSON(),
            'returned_at' => $this->returned_at?->toJSON(),
            'loan_status' => $this->loan_status,
            'book_copy' => $this->whenLoaded('bookCopy', fn () => $this->bookCopy ? [
                'id' => $this->bookCopy->id,
                'accession_no' => $this->bookCopy->accession_no,
                'barcode' => $this->bookCopy->barcode,
                'book' => $this->bookCopy->book ? [
                    'id' => $this->bookCopy->book->id,
                    'title' => $this->bookCopy->book->title,
                    'author' => $this->bookCopy->book->author,
                ] : null,
            ] : null),
        ];
    }
}
