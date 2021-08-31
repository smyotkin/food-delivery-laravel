<x-app-layout>
    <x-slot name="title">Главная</x-slot>
    <x-slot name="header"></x-slot>

    <div class="container-fluid bg-gradient-ferone min-vh-100 d-flex align-items-center" style="padding-top: 55px">
        <div class="container">
            <div class="row">
                @php ($controlCount = $control->count())

                @if ($controlCount > 0)
                    <div class="col-{{ $controlCount * 2 }} d-flex align-items-center rounded rounded-5" style="background-color: rgba(0, 0, 0, 0.3)">
                        <div class="w-100">
                            <div class="row">
                                @foreach($control as $module)
                                    <div class="col-12 col-md-{{ 12 / $controlCount }}">
                                        <a href="{{ $module->url }}" class="text-decoration-none text-center text-white fs-5">
                                            <div class="text-center">
                                                <img src="/img/{{ $module->slug }}-icon.png" class="rounded rounded-5 shadow module_img">
                                            </div>

                                            <div class="name mt-3 lh-1 text-shadow">{{ $module->name }}</div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @foreach($other as $module)
                    <div class="col-12 col-md-2 d-flex align-items-center justify-content-center py-4">
                        <a href="{{ $module->url }}" class="text-decoration-none text-center text-white fs-5 module_link">
                            <div class="text-center">
                                <img src="/img/{{ $module->slug }}-icon.png" class="rounded rounded-5 shadow module_img">
                            </div>

                            <div class="name mt-3 lh-1 text-shadow">{{ $module->name }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
