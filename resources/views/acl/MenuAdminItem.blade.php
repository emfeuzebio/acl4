<div class="menu-item" data-menu-id="{{ $menu->id }}">
    <div>
        <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
        @if($menu->icon)
            <i class="{{ $menu->icon }}"></i>
        @endif
        {{ $menu->name }}
        @if($menu->route)
            <small class="text-muted">({{ $menu->route }})</small>
        @endif
    </div>
    <div class="actions">
        <button class="btn btn-sm btn-outline-primary" onclick="editMenu({{ $menu->id }}, '{{ $menu->name }}', '{{ $menu->icon }}', '{{ $menu->route }}')">
            <i class="fas fa-edit"></i>
        </button>
    </div>
</div>

@if($menu->children->count() > 0)
    <div class="nestable-list">
        @foreach($menu->children as $child)
            @include('acl/MenuAdminItem', ['menu' => $child, 'level' => $level + 1])
        @endforeach
    </div>
@endif