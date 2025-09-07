@foreach($menus->whereNull('parent_id')->sortBy('pivot.position') as $menu)
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
            <button class="btn btn-sm btn-outline-danger" onclick="removeMenuFromRole({{ $menu->id }})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    @if($menu->children->count() > 0)
        <div class="nestable-list">
            @foreach($menu->children->sortBy('pivot.position') as $child)
                <div class="menu-item" data-menu-id="{{ $child->id }}">
                    <div>
                        <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                        @if($child->icon)
                            <i class="{{ $child->icon }}"></i>
                        @endif
                        {{ $child->name }}
                        @if($child->route)
                            <small class="text-muted">({{ $child->route }})</small>
                        @endif
                    </div>
                    <div class="actions">
                        <button class="btn btn-sm btn-outline-danger" onclick="removeMenuFromRole({{ $child->id }})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endforeach