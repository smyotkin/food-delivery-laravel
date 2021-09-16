@if (!empty($formatted))
    @foreach($formatted as $setting)
        <li class="settings_option-block list-group-item d-md-flex justify-content-between align-items-center py-3 px-1 position-relative">
            <div class="me-auto">
                <div class="fw-bold {{ !has_permission('settings_modify') ? 'text-muted' : '' }}">{{ $setting['name'] }}</div>
            </div>

            <form class="position-relative setting_option-form" method="post" action="{{ route('settings.update', ['setting' => $setting['key']]) }}">
                @csrf
                @php ($formattedValue = isset($setting['data']) ? $setting['data'][$setting['value']] : $setting['value'])

                @if (has_permission('settings_modify'))
                    <a href="javascript:" onclick="$(this).hide()" class="mt-2 mt-md-0 settings_option text-decoration-none btn btn-outline-dark rounded-pill px-3">{{ !empty($formattedValue) ? $formattedValue : 'Не задано' }}</a>

                    @if ($setting['type'] == 'select')
                        <select class="d-none form-select mt-2 mt-md-0" name="{{ $setting['key'] }}" id="">
                            @foreach ($setting['data'] as $key => $value)
                                <option value="{{ $key }}" {{ $key == $setting['value'] ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    @elseif ($setting['type'] == 'textarea')
                        <textarea class="d-none form-control mt-2 mt-md-0" name="{{ $setting['key'] }}" id="" cols="30" rows="10"></textarea>
                    @else
                        <input class="d-none form-control mt-2 mt-md-0" name="{{ $setting['key'] }}" type="{{ $setting['type'] ?? 'text' }}" value="{{ $setting['value'] ?? $setting['default'] }}" {{ isset($setting['min']) ? 'min=' . $setting['min'] : '' }} {{ isset($setting['max']) ? 'max=' . $setting['max'] : '' }}>
                    @endif
                @else
                    <span class="text-decoration-none btn btn-outline-dark rounded-pill px-3 disabled mt-2 mt-md-0">{{ $formattedValue }}</span>
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
