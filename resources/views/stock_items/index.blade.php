<td class="text-end">
    @can('update', $item)
        <a href="{{ route('stock-items.edit', $item->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
    @endcan
    @can('delete', $item)
        <form action="{{ route('stock-items.destroy', $item->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Видалити цей товар?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
        </form>
    @endcan
</td>