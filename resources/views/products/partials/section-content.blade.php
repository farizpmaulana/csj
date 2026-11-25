@if ($section->type === \App\Enums\ProductSectionType::Table)
    <table class="detail-table">
        @foreach ($section->rows as $row)
            <tr>
                <th>{{ $row->label }}</th>
                <td>{{ $row->value }}</td>
            </tr>
        @endforeach
    </table>
@elseif ($section->type === \App\Enums\ProductSectionType::Ordered)
    <ol class="detail-list">
        @foreach ($section->rows as $row)
            <li>{{ $row->label }}</li>
        @endforeach
    </ol>
@else
    <ul class="detail-list">
        @foreach ($section->rows as $row)
            <li>{{ $row->label }}</li>
        @endforeach
    </ul>
@endif
