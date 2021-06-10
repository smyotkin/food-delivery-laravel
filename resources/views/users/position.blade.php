<x-app-layout>
    <x-slot name="back_href">{{ route('positions.index') }}</x-slot>
    <x-slot name="back_title">Должности</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Информация о должности</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $role->name ?? 'Название' }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ $role->slug ?? 'Метка' }}</h6>
                </div>
            </div>
            <div class="col text-end lh-base">
                <p class="mb-1">
                    <a href="javascript:" onclick="event.preventDefault(); $('#position_form').submit();" id="save" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                </p>
                @if (isset($role))
                    <p class="mb-0 text-muted">
                        @php ($created_at = Date::parse($role->created_at))

                        <small>
                            Добавлена: {{ $created_at->format(now()->year == $created_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>
                    <p class="mb-0 text-muted">
                        @php ($updated_at = Date::parse($role->updated_at))

                        <small>
                            Обновлено: {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>
                @endif
            </div>
        </div>
    </div>


    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col">
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                    <form method="post" action="{{ isset($role) ? route('positions.update', ['position' => $role]) : route('positions.store') }}" id="position_form" class="row g-3 update_position">
                    @method(isset($role) ? 'patch' : 'post')
                    @csrf

                    @if (isset($role))
                        <input type="hidden" name="id" value="{{ $role->id }}">
                    @endif

                    <div class="col-12">
                        <label for="name" class="form-label">Название</label>
                        <input type="text" class="form-control rounded-0" id="name" name="name" value="{{ $role->name ?? '' }}" placeholder="Название">
                    </div>

                    <div class="col-12">
                        <label for="slug" class="form-label">Метка</label>
                        <input type="text" class="form-control rounded-0" id="slug" name="slug" value="{{ $role->slug ?? '' }}" placeholder="Метка (латиницей с нижним подчеркиванием)">
                    </div>

                    <div class="col-12">
                        <label for="status" class="form-label">Статус</label>
                        <select class="form-select" id="status" name="status" required>
                            <option disabled selected>Ничего не выбрано</option>
                            @forelse (config('custom.statuses') as $key => $status)
                                <option value="{{ $key }}" {{ isset($role->status) && $role->status == $key ? 'selected' : '' }}>{{ $status['name'] }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
