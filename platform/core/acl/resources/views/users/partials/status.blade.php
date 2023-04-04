<a data-type="select"
   data-source="{{ json_encode(Botble\ACL\Enums\UserStatusEnum::$STATUSES) }}"
   data-pk="{{ $item->id }}"
   data-url="{{ route('users.changeStatus') }}"
   data-value="{{ $item->status }}"
   data-title="Change Status"
   class="editable"
   href="#">
    {{ ucwords($item->status) }}
</a>
