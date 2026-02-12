<?php

namespace App\Filament\Widgets;

use App\Models\CnpjQuery;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Model;

class TopCnpjsTable extends TableWidget
{
    protected static ?string $heading = 'Top 10 CNPJs Consultados';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = ['lg' => 1, 'default' => 'full'];

    public function getTableRecordKey(Model|array $record): string
    {
        return $record->cnpj;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CnpjQuery::query()
                    ->select('cnpj')
                    ->selectRaw('MAX(razao_social) as razao_social')
                    ->selectRaw('COUNT(*) as total_consultas')
                    ->selectRaw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as sucessos')
                    ->selectRaw('MAX(queried_at) as ultima_consulta')
                    ->groupBy('cnpj')
                    ->orderByDesc('total_consultas')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->formatStateUsing(fn (string $state): string => preg_replace(
                        '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
                        '$1.$2.$3/$4-$5',
                        $state
                    )),

                TextColumn::make('razao_social')
                    ->label('Razão Social')
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('total_consultas')
                    ->label('Consultas')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('sucessos')
                    ->label('Sucessos')
                    ->alignCenter(),

                TextColumn::make('ultima_consulta')
                    ->label('Última')
                    ->since(),
            ])
            ->paginated(false)
            ->defaultSort('total_consultas', 'desc')
            ->defaultKeySort(false);
    }
}
