@php
    $configData = Helper::applClasses();
@endphp
<div class="main-menu menu-fixed {{ $configData['theme'] === 'dark' ? 'menu-dark' : 'menu-light' }} menu-accordion menu-shadow"
    data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="{{ url('admin/') }}">
                    <span class="brand-logo">
                        <img src="{{ asset('images/logo/logo-new.png') }}" alt="">
                    </span>
                    <h4 class="brand-text">{{ Str::ucfirst(env('APP_NAME')) }}</h4>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- Foreach menu item starts --}}
            @if (isset($menuData[0]))
               
                @foreach ($menuData[0]->menu as $menu)
             
                    @if (isset($menu->navheader))
                        <li class="navigation-header">
                            <span>{{ $menu->navheader }}</span>
                            <i data-feather="more-horizontal"></i>
                        </li>
                    @else
                        {{-- Add Custom Class with nav-item --}}
                        @php
                            $custom_classes = '';
                            if (isset($menu->classlist)) {
                                $custom_classes = $menu->classlist;
                            }
                        @endphp
                        @if (auth("admin")->user()->role == "admin" && $menu->isAdmin==true)
                          
                        <li
                            class="nav-item {{ Route::currentRouteName() === $menu->slug ? 'active' : '' }} {{ $custom_classes }}">
                            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0)' }}"
                                class="d-flex align-items-center"
                                target="{{ isset($menu->newTab) ? '_blank' : '_self' }}">
                                <i class="side-bar-icon">
                                    {!! $menu->icon !!}
                                </i>
                                <span class="menu-title text-truncate">{{ $menu->name}}</span>
                                @if (isset($menu->badge))
                                    <?php $badgeClasses = 'badge badge-pill badge-light-primary ml-auto mr-1'; ?>
                                    <span
                                        class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }} ">{{ $menu->badge }}</span>
                                @endif
                            </a>
                            @if (isset($menu->submenu))
                                @include('panels/submenu', ['menu' => $menu->submenu])
                            @endif
                        </li>
                        @else
                      
                        @endif


                        @if (auth("admin")->user()->role == "superadmin")
                          
                        <li
                            class="nav-item {{ Route::currentRouteName() === $menu->slug ? 'active' : '' }} {{ $custom_classes }}">
                            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0)' }}"
                                class="d-flex align-items-center"
                                target="{{ isset($menu->newTab) ? '_blank' : '_self' }}">
                                <i class="side-bar-icon">
                                    {!! $menu->icon !!}
                                </i>
                                <span class="menu-title text-truncate">{{ $menu->name}}</span>
                                @if (isset($menu->badge))
                                    <?php $badgeClasses = 'badge badge-pill badge-light-primary ml-auto mr-1'; ?>
                                    <span
                                        class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }} ">{{ $menu->badge }}</span>
                                @endif
                            </a>
                            @if (isset($menu->submenu))
                                @include('panels/submenu', ['menu' => $menu->submenu])
                            @endif
                        </li>
                        @else
                      
                        @endif
                    @endif
                @endforeach
            @endif
            {{-- Foreach menu item ends --}}
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
