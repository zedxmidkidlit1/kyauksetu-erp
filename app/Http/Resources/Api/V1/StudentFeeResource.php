<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeeResource extends JsonResource
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
            'amount' => $this->amount,
            'discount_amount' => $this->discount_amount,
            'payable_amount' => $this->payable_amount,
            'due_at' => $this->due_at?->toDateString(),
            'fee_status' => $this->fee_status,
            'academic_year' => $this->whenLoaded('academicYear', fn () => $this->academicYear ? [
                'id' => $this->academicYear->id,
                'name' => $this->academicYear->name,
            ] : null),
            'semester' => $this->whenLoaded('semester', fn () => $this->semester ? [
                'id' => $this->semester->id,
                'name' => $this->semester->name,
            ] : null),
            'fee_type' => $this->whenLoaded('feeType', fn () => $this->feeType ? [
                'id' => $this->feeType->id,
                'name' => $this->feeType->name,
                'code' => $this->feeType->code,
                'category' => $this->feeType->fee_category,
            ] : null),
            'payments' => $this->whenLoaded('studentPayments', fn () => $this->studentPayments->map(fn ($payment): array => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'paid_at' => $payment->paid_at?->toJSON(),
            ])->values()),
        ];
    }
}
