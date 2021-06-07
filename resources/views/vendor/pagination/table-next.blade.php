@if ($paginator->hasPages() && $paginator->hasMorePages())
    <tr class="table_pagination">
        <td class="bg-white border-0 py-3" colspan="6">
            <a class="btn btn-link text-decoration-none px-0" href="{{ $paginator->nextPageUrl() }}" rel="next">
                Показать еще 100 пользователей
            </a>
        </td>
    </tr>
@endif
