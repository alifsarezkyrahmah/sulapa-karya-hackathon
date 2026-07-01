{{-- resources/views/components/alert.blade.php --}}
@if (session('success'))
<div class="toast toast-top toast-center z-[100] mt-20">
  <div class="alert bg-forest-50 border border-forest-200 text-forest-700 shadow-lg">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <span>{{ session('success') }}</span>
  </div>
</div>
@endif

@if ($errors->any())
<div class="toast toast-top toast-center z-[100] mt-20">
  <div class="alert bg-terracotta-50 border border-terracotta-200 text-terracotta-700 shadow-lg">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ $errors->first() }}</span>
  </div>
</div>
@endif

<script>
  setTimeout(() => document.querySelectorAll('.toast').forEach(t => t.style.display = 'none'), 4000);
</script>