<header class="site-header">
    <div class="site-header__inner">
        <a class="site-logo" href="{{ route('home') }}" aria-label="BelajarData home">BelajarData</a>
        <button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="site-navigation" data-nav-toggle>
            <span>Menu</span>
        </button>
        <nav id="site-navigation" class="site-navigation" aria-label="Navigasi utama" data-site-nav>
            <a class="{{ $active === 'learn' ? 'is-active' : '' }}" href="{{ route('learning.index') }}" @if ($active === 'learn') aria-current="page" @endif>Learn</a>
            <a class="{{ $active === 'skills' ? 'is-active' : '' }}" href="{{ route('skills.index') }}" @if ($active === 'skills') aria-current="page" @endif>Explore Skills</a>
            <a class="{{ $active === 'projects' ? 'is-active' : '' }}" href="{{ route('projects.index') }}" @if ($active === 'projects') aria-current="page" @endif>Projects</a>
            @auth
                <span class="site-account">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="site-navigation__logout" type="submit">Keluar</button></form>
            @else
                <a href="{{ route('login') }}">Masuk</a>
            @endauth
        </nav>
    </div>
</header>
