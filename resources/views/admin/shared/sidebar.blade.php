<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>
            @foreach($menu as $section)
                @if (count($section['roles']) > 1)
                    <li class="nav-item start">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="{{ $section['image'] }}"></i>
                            <span class="title">{{ $section['name'] }}</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            @foreach ($section['roles'] as $role)
                                <li class="nav-item start {{ $role['class'] }}">
                                    <a href="{{ route($role['route']) }}" class="nav-link">
                                        <i class="{{ $role['image'] }}"></i>
                                        <span class="title">{{ $role['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="nav-item {{$section['roles'][0]['class'] }}">
                        <a href="{{ route($section['roles'][0]['route']) }}" class="nav-link nav-toggle">
                            <i class="{{ $section['roles'][0]['image'] }}"></i>
                            <span class="title">{{ $section['roles'][0]['title'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>