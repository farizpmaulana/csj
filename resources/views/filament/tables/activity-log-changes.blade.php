@php
    $record = $getRecord();
    $changes = $record?->changes ?? [];
    $old = $changes['old'] ?? [];
    $new = $changes['new'] ?? [];
    $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
@endphp

<div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem;">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
        <div><strong>User:</strong> {{ $record->user_name }}</div>
        <div><strong>Action:</strong> {{ ucfirst($record->action) }}</div>
        <div><strong>When:</strong> {{ $record->created_at?->format('d M Y, H:i:s') }}</div>
    </div>

    @if ($keys !== [])
        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 0.5rem 0.75rem; font-weight: 600;">Field</th>
                        @if ($old !== [])
                            <th style="padding: 0.5rem 0.75rem; font-weight: 600;">Before</th>
                        @endif
                        @if ($new !== [])
                            <th style="padding: 0.5rem 0.75rem; font-weight: 600;">After</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($keys as $key)
                        @php
                            $beforeVal = $old[$key] ?? null;
                            $afterVal = $new[$key] ?? null;
                            $formatVal = fn ($val): string => is_array($val)
                                ? json_encode($val, JSON_UNESCAPED_UNICODE)
                                : (is_bool($val) ? ($val ? 'true' : 'false') : (string) ($val ?? '—'));
                        @endphp
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.5rem 0.75rem; font-weight: 600; white-space: nowrap;">{{ $key }}</td>
                            @if ($old !== [])
                                <td style="padding: 0.5rem 0.75rem; word-break: break-word;">{{ $formatVal($beforeVal) }}</td>
                            @endif
                            @if ($new !== [])
                                <td style="padding: 0.5rem 0.75rem; word-break: break-word;">{{ $formatVal($afterVal) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: #64748b;">No field-level changes recorded.</p>
    @endif
</div>
