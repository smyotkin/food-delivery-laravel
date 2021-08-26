@if ($paginator->hasPages() && $paginator->hasMorePages())
    @php($countPerPage = $paginator->perPage())

    <tr class="table_pagination">
        <td class="bg-white border-0 py-3" colspan="7">
            <a class="btn btn-link text-decoration-none px-0" href="{{ $paginator->nextPageUrl() }}" rel="next">
                Показать еще {{ $countPerPage }}
{{--                {{ Lang::choice('строку|строки|строк', $countPerPage) }}--}}
            </a>
        </td>
    </tr>
@endif
