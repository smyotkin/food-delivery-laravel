@if(!empty($data) && $data->count())
    @foreach($data as $city)
        <tr class="{{ $city->is_active == 0 ? 'text-muted' : '' }}" data-page="{{ $data->currentPage() }}">
            <td>
                @permission('cities_view')
                    <a href="/cities/{{ $city->id }}" class="text-decoration-none">{{ $city->name }}</a>
                @else_permission
                    {{ $city->name }}
                @endpermission
            </td>
            <td>{{ $city->phone_formatted }}</td>
            <td>{{ $city->timezone_formatted }}</td>
            <td>{{ $city->folder }}</td>
        </tr>
    @endforeach

    {{ $data->links('vendor.pagination.table-next') }}
@else
    <tr>
        <td colspan="6" class="text-center">Не найдено</td>
    </tr>
@endif
