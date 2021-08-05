@if (!empty($formatted))
    @foreach($formatted as $setting)
        <li class="settings_option-block list-group-item d-flex justify-content-between align-items-center py-3 px-1 position-relative">
            <div class="me-auto">
                <div class="fw-bold {{ !has_permission('settings_modify') ? 'text-muted' : '' }}">{{ $setting['name'] }}</div>
            </div>

            <form class="position-relative" method="post" action="{{ route('settings.update', ['setting' => $setting['key']]) }}">
                @csrf
                @php ($formattedValue = isset($setting['data']) ? $setting['data'][$setting['value']] : $setting['value'])

                @if (has_permission('settings_modify'))
                    <a href="javascript:" onclick="$(this).hide()" class="settings_option text-decoration-none btn btn-outline-dark rounded-pill px-3">{{ $formattedValue }}</a>

                    @if ($setting['type'] == 'select')
                        <select class="d-none form-select" name="{{ $setting['key'] }}" id="">
                            @foreach ($setting['data'] as $key => $value)
                                <option value="{{ $key }}" {{ $key == $setting['value'] ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    @elseif ($setting['type'] == 'textarea')
                        <textarea class="d-none form-control" name="{{ $setting['key'] }}" id="" cols="30" rows="10"></textarea>
                    @else
                        <input class="d-none form-control" name="{{ $setting['key'] }}" type="{{ $setting['type'] ?? 'text' }}" value="{{ $setting['value'] ?? $setting['default'] }}">
                    @endif
                @else
                    <span class="text-decoration-none btn btn-outline-dark rounded-pill px-3 disabled">{{ $formattedValue }}</span>
                @endif
            </form>

            @if (has_permission('settings_modify'))
                <a href="javascript:" class="save-icon position-absolute start-100 h-100 ms-2" style="display: none"></a>
            @endif
        </li>
    @endforeach

    {{ $settings->links('vendor.pagination.table-next') }}
@else
    <li class="list-group-item py-4 px-0">
        Настройки не найдены
    </li>
@endif
