<?php

namespace App\Filament\Resources\StaffLeaveRequests;

use App\Filament\Resources\StaffLeaveRequests\Pages\CreateStaffLeaveRequest;
use App\Filament\Resources\StaffLeaveRequests\Pages\EditStaffLeaveRequest;
use App\Filament\Resources\StaffLeaveRequests\Pages\ListStaffLeaveRequests;
use App\Filament\Resources\StaffLeaveRequests\Schemas\StaffLeaveRequestForm;
use App\Filament\Resources\StaffLeaveRequests\Tables\StaffLeaveRequestsTable;
use App\Models\StaffLeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StaffLeaveRequestResource extends Resource
{
    protected static ?string $model = StaffLeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'HR';

    public static function form(Schema $schema): Schema
    {
        return StaffLeaveRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffLeaveRequestsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'approvedBy',
                'staffProfile',
                'teacherProfile',
                'user',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffLeaveRequests::route('/'),
            'create' => CreateStaffLeaveRequest::route('/create'),
            'edit' => EditStaffLeaveRequest::route('/{record}/edit'),
        ];
    }
}
