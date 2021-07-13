@if(!empty($settings) && $settings->count())
    @foreach($settings as $setting)
        <tr data-page="{{ $settings->currentPage() }}">
            <td>{{ $setting->key }}</td>
            <td>{{ $setting->value }}</td>
        </tr>
    @endforeach

    {{ $settings->links('vendor.pagination.table-next') }}
@else
    <tr>
        <td colspan="2" class="text-center">Настройки не найдены</td>
    </tr>
@endif
