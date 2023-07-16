<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Auth::user()->currentCompany->id;
        $data['type'] = 'invoice';
        $data['status'] = 'draft';
        $data['amount'] = 0;
        $data['created_by'] = Auth::id();

        return $data;
    }
}