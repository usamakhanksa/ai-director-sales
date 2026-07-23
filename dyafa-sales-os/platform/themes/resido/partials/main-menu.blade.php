<ul {!! $options !!}>
    @foreach ($menu_nodes as $key => $row)
        <li class="nav-item {{ $row->css_class }}">
            <a class="nav-link @if ($row->active) active text-orange @endif" href="{{ $row->url }}"
                target="{{ $row->target }}">
                {!! $row->icon_html !!}
                <span class="menu-title">{{ $row->title }}</span>
                @if ($row->active)
                    <span class="sr-only">(current)</span>
                @endif
            </a>
        </li>
    @endforeach
</ul>
