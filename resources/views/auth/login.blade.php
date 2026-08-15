<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Kamera 24 — Kirish</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: { accent: '#B5502A', ink: '#1A1614' },
        fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
        borderRadius: { DEFAULT: '8px', md: '12px', lg: '18px' },
      }
    }
  }
</script>
<style>
  body{font-variant-numeric:tabular-nums;-webkit-font-smoothing:antialiased;}
  * { -webkit-tap-highlight-color: transparent; }
  button { touch-action: manipulation; }
  button:not(:disabled):active { transform: scale(0.97); }
  @media (max-width: 767px) { input { font-size: 16px !important; } }
  input:focus {
    outline: none;
    border-color: #B5502A;
    box-shadow: 0 0 0 3px rgba(181,80,42,0.15);
  }
</style>
</head>
<body class="bg-[#FAFAF7] text-ink font-sans min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-[360px]">
    <div class="flex flex-col items-center mb-6">
      <div class="w-11 h-11 rounded bg-ink flex items-center justify-center mb-2.5">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FAFAF7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/><path d="M4 12v8a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-8"/></svg>
      </div>
      <div class="text-base font-bold">Kamera 24</div>
      <div class="text-[11px] text-ink/55">Mobil aksessuarlar</div>
    </div>

    <div class="bg-white border border-ink/10 rounded-md p-4.5">
      <div class="text-sm font-bold mb-3.5">Tizimga kirish</div>

      @if ($errors->any())
        <div class="mb-3 px-3 py-2.5 bg-[#FDEDE9] border border-red-700/30 rounded text-[12.5px] text-red-700 font-medium">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="/login">
        @csrf
        <label class="text-[11.5px] text-ink/60 font-semibold">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] my-1 mb-2.5">

        <label class="text-[11.5px] text-ink/60 font-semibold">Parol</label>
        <input type="password" name="password" required
               class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] my-1 mb-3.5">

        <button type="submit" class="w-full p-3 rounded-md bg-accent text-[#FAFAF7] text-sm font-bold min-h-[44px]">Kirish</button>
      </form>
    </div>
  </div>

</body>
</html>
