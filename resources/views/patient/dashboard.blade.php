<p>Welcome, {{ auth()->user()->full_name }}</p>
<p>Here is your patient dashboard.</p>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Logout Button -->
<a href="#" class="btn btn-danger"
    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Logout
</a>
