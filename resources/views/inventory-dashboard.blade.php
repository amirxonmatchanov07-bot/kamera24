<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Kamera 24  — Boshqaruv paneli</title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
  [x-cloak]{display:none!important;}
  ::-webkit-scrollbar{width:0;height:0;}
  * { -webkit-tap-highlight-color: transparent; }
  button, a { touch-action: manipulation; }
  button:not(:disabled):active { transform: scale(0.97); }
  /* iOS Safari kichik shriftli inputlarga fokus qilinganda avtomatik zoom qilib yuboradi - oldini olamiz */
  @media (max-width: 767px) {
    input, select, textarea { font-size: 16px !important; }
  }
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; appearance: none; margin: 0; }
  input[type=number] { -moz-appearance: textfield; appearance: textfield; }
  input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #B5502A;
    box-shadow: 0 0 0 3px rgba(181,80,42,0.15);
  }
  .safe-top { padding-top: env(safe-area-inset-top); }
  .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
  @media print {
    body * { visibility: hidden; }
    #receipt-print, #receipt-print * { visibility: visible; }
    #receipt-print { position: fixed; top: 0; left: 0; width: 100%; }
  }
</style>
</head>
<body class="bg-[#FAFAF7] text-ink font-sans min-h-screen" x-data="dashboard({{ Illuminate\Support\Js::from(['role' => auth()->user()->role, 'name' => auth()->user()->name]) }})" x-init="init()">

  <!-- Header -->
  <div class="safe-top sticky top-0 z-20 flex items-center justify-between px-4 lg:px-6 py-3 bg-[#FAFAF7]/85 backdrop-blur-md border-b border-ink/10">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded bg-ink flex items-center justify-center">
        <i data-lucide="store" class="w-[18px] h-[18px] text-[#FAFAF7]"></i>
      </div>
      <div>
        <div class="text-sm font-bold leading-tight">Kamera 24</div>
        <div class="text-[11px] text-ink/55 leading-tight">Mobil aksessuarlar</div>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <div x-show="offline" x-cloak class="flex items-center gap-1 px-2.5 py-1 bg-[#FDEDE9] border border-red-700/30 rounded">
        <i data-lucide="wifi-off" class="w-[13px] h-[13px] text-red-700"></i>
        <span class="text-[11px] font-semibold text-red-700">Oflayn</span>
      </div>
      <button class="w-[38px] h-[38px] border border-ink/10 bg-white rounded flex items-center justify-center">
        <i data-lucide="bell" class="w-[19px] h-[19px] text-ink"></i>
      </button>
      <form method="POST" action="/logout">
        @csrf
        <button type="submit" class="w-[38px] h-[38px] border border-ink/10 bg-white rounded flex items-center justify-center" title="Chiqish">
          <i data-lucide="log-out" class="w-[18px] h-[18px] text-ink"></i>
        </button>
      </form>
    </div>
  </div>

  <div class="flex items-start max-w-[1280px] mx-auto">

    <!-- Desktop sidebar -->
    <div class="hidden lg:flex w-[220px] shrink-0 sticky top-[57px] h-[calc(100vh-57px)] px-3 py-4 flex-col border-r border-ink/10">
      <template x-for="item in navItems" :key="item.id">
        <button @click="nav = item.id"
          class="flex items-center gap-2.5 w-full px-3.5 py-2.5 rounded mb-0.5 text-left"
          :class="nav === item.id ? 'bg-accent/10' : ''">
          <i :data-lucide="item.icon" class="w-[18px] h-[18px]" :class="nav === item.id ? 'text-accent' : 'text-ink/55'"></i>
          <span class="text-[13px]" :class="nav === item.id ? 'font-bold text-ink' : 'font-medium text-ink/65'" x-text="item.label"></span>
        </button>
      </template>
      <div class="flex-1"></div>
      <button @click="nav = 'sotuv'" class="flex items-center justify-center gap-2 p-3 rounded bg-accent">
        <i data-lucide="plus" class="w-[18px] h-[18px] text-[#FAFAF7]"></i>
        <span class="text-[13px] font-bold text-[#FAFAF7]">Yangi sotuv</span>
      </button>
    </div>

    <!-- Main -->
    <div class="flex-1 min-w-0 px-4 lg:px-6 py-3.5 lg:py-5">

      <!-- ============ DASHBOARD ============ -->
      <div x-show="nav === 'dashboard'" x-cloak>

        <!-- Hero stat row -->
        <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-2.5 mb-2.5">
          <template x-if="role === 'admin'">
            <div class="bg-ink rounded-md p-4">
              <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#FAFAF7]/75 tracking-wide">Sof foyda (bugun)</span>
                <div class="flex items-center gap-1 text-[11.5px] font-bold text-emerald-400" x-show="profitChangePct !== null">
                  <i data-lucide="trending-up" class="w-3 h-3"></i>
                  <span x-text="profitChangePct + '%'"></span>
                </div>
              </div>
              <div class="text-[34px] font-extrabold text-[#FAFAF7] tracking-tight mt-1.5 leading-none" x-text="money(profitToday)"></div>
              <div class="text-xs text-[#FAFAF7]/60 mt-1">Kecha: <span x-text="money(profitYesterday)"></span></div>
            </div>
          </template>
          <div class="bg-white border border-ink/10 rounded-md p-4">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-ink/60 tracking-wide">Bugungi savdo</span>
              <div class="flex items-center gap-1 text-[11.5px] font-bold text-emerald-700" x-show="salesChangePct !== null">
                <i data-lucide="trending-up" class="w-3 h-3"></i>
                <span x-text="salesChangePct + '%'"></span>
              </div>
            </div>
            <div class="text-[26px] font-extrabold tracking-tight mt-1.5 leading-none" x-text="money(salesToday)"></div>
            <div class="text-xs text-ink/50 mt-1">Kecha: <span x-text="money(salesYesterday)"></span></div>
          </div>
        </div>

        <!-- Secondary stats 2x2 -->
        <div class="grid grid-cols-2 gap-2.5 mb-2.5">
          <div class="bg-white border border-ink/10 rounded-md p-2.5">
            <div class="flex items-center gap-1.5 min-w-0">
              <i data-lucide="receipt" class="w-[15px] h-[15px] text-ink/50 shrink-0"></i>
              <span class="text-[10px] font-medium text-ink/60 whitespace-nowrap">Bugungi cheklar</span>
            </div>
            <div class="text-[17px] font-bold mt-1.5 whitespace-nowrap" x-text="receiptsToday"></div>
          </div>
          <button @click="nav = 'mijozlar'" class="bg-white border border-ink/10 rounded-md p-2.5 text-left min-h-[44px]">
            <div class="flex items-center justify-between gap-1.5 min-w-0">
              <div class="flex items-center gap-1.5 min-w-0">
                <i data-lucide="hand-coins" class="w-[15px] h-[15px] text-ink/50 shrink-0"></i>
                <span class="text-[10px] font-medium text-ink/60 whitespace-nowrap">Nasiya</span>
              </div>
              <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-ink/35 shrink-0"></i>
            </div>
            <div class="text-[17px] font-bold mt-1.5 whitespace-nowrap" x-text="money(totalDebt)"></div>
          </button>
        </div>

        <!-- 14-day trend -->
        <div class="bg-white border border-ink/10 rounded-md p-3.5 mb-2.5">
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-[13.5px] font-bold">14 kunlik savdo</span>
            <span class="text-[11.5px] text-ink/50">dollarda</span>
          </div>
          <div class="flex items-end gap-1 h-[120px] pt-2">
            <template x-for="(bar, i) in chartData" :key="i">
              <div class="flex-1 flex flex-col items-center justify-end h-full gap-1">
                <div class="w-full h-full flex items-end">
                  <div class="w-full rounded-t"
                       :class="i === chartData.length - 1 ? 'bg-accent' : 'bg-ink/15'"
                       :style="`height:${Math.max(6,(bar.v/chartMax)*100)}%`"></div>
                </div>
                <span class="text-[9px]" :class="i === chartData.length - 1 ? 'font-bold text-ink/70' : 'font-medium text-ink/40'" x-text="bar.label"></span>
              </div>
            </template>
          </div>
        </div>

        <!-- Recent sales -->
        <div class="bg-white border border-ink/10 rounded-md p-3.5 mb-2.5">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-[13.5px] font-bold">Oxirgi sotuvlar</span>
              <button @click="nav = 'hisobot'" class="text-[11.5px] font-semibold text-accent">Barchasini ko'rish</button>
            </div>

            <!-- Loading skeleton -->
            <template x-if="salesState === 'loading'">
              <div>
                <template x-for="i in 5" :key="i">
                  <div class="flex items-center gap-2.5 py-2 border-t border-ink/10">
                    <div class="h-[11px] w-[55%] bg-ink/10 rounded"></div>
                    <div class="h-3.5 w-[70px] bg-ink/10 rounded ml-auto"></div>
                  </div>
                </template>
              </div>
            </template>

            <!-- Empty -->
            <template x-if="salesState === 'empty'">
              <div class="flex flex-col items-center gap-2 py-8 px-4">
                <i data-lucide="inbox" class="w-7 h-7 text-ink/30"></i>
                <span class="text-[12.5px] text-ink/50 text-center">Bugun hali sotuv bo'lmagan</span>
              </div>
            </template>

            <!-- Normal -->
            <template x-if="salesState === 'normal'">
              <div>
                <template x-for="s in recentSales" :key="s.id">
                  <button @click="viewReceipt(s.id)" class="w-full flex items-center gap-2.5 py-2 border-t border-ink/10 text-left min-h-[44px]">
                    <div class="w-11 shrink-0 text-[11.5px] text-ink/50 font-medium" x-text="s.time"></div>
                    <div class="flex-1 min-w-0 text-[12.5px] truncate" x-text="s.summary"></div>
                    <div class="shrink-0 text-[10px] font-bold px-1.5 py-1 rounded"
                         :class="payBadge(s.payType)" x-text="s.payType"></div>
                    <div class="w-24 text-right shrink-0 text-[13.5px] font-bold" x-text="money(s.total)"></div>
                  </button>
                </template>
              </div>
            </template>
          </div>

      </div>

      <!-- ============ SOTUV ============ -->
      <div x-show="nav === 'sotuv'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-3">
          <div class="min-w-0">
            <input type="text" x-model="saleSearch" placeholder="Mahsulot qidirish..."
                   class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px]">
            <div class="flex gap-1.5 overflow-x-auto mt-2 pb-0.5">
              <template x-for="c in saleCategories" :key="c">
                <button @click="saleCategory = c"
                  class="px-3 py-1.5 rounded border text-xs font-semibold whitespace-nowrap"
                  :class="saleCategory === c ? 'bg-accent text-[#FAFAF7] border-accent' : 'border-ink/15 text-ink/65'"
                  x-text="c + ' (' + categoryCount(c) + ')'"></button>
              </template>
            </div>
            <div class="bg-white border border-ink/10 rounded-md mt-2.5 divide-y divide-ink/10">
              <template x-if="filteredSaleProducts.length === 0">
                <div class="flex flex-col items-center gap-2 py-8 px-4">
                  <i data-lucide="search-x" class="w-6 h-6 text-ink/30"></i>
                  <span class="text-[12.5px] text-ink/50 text-center">Mahsulot topilmadi</span>
                </div>
              </template>
              <template x-for="p in filteredSaleProducts" :key="p.id">
                <button @click="addToCart(p)" class="w-full flex items-center gap-2.5 py-2.5 px-3 text-left min-h-[44px] hover:bg-[#F9F7F2]">
                  <div class="w-14 h-14 shrink-0 bg-[#F1EFEA] rounded overflow-hidden flex items-center justify-center">
                    <img :src="p.image_url" x-show="p.image_url" class="w-full h-full object-cover">
                    <i :data-lucide="p.icon" x-show="!p.image_url" class="w-6 h-6 text-ink/40"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold truncate" x-text="p.name"></div>
                    <div class="text-[11px] text-ink/50" x-text="p.category"></div>
                  </div>
                  <div class="text-[13px] font-bold shrink-0" x-text="money(p.price)"></div>
                  <div class="shrink-0 w-8 h-8 rounded-full bg-accent/10 flex items-center justify-center">
                    <i data-lucide="plus" class="w-4 h-4 text-accent"></i>
                  </div>
                </button>
              </template>
            </div>
          </div>
          <!-- Desktop: doim ko'rinadigan savat paneli -->
          <div class="hidden lg:block bg-white border border-ink/10 rounded-md p-3.5 h-fit">
            <div class="text-[13.5px] font-bold mb-2" x-text="'Savat (' + cartCount + ')'"></div>
            <template x-if="cart.length === 0">
              <div class="text-[12.5px] text-ink/45 py-4 text-center">Savat bo'sh</div>
            </template>
            <template x-if="cart.length > 0">
              <div>
                <template x-for="c in cart" :key="c.id">
                  <div class="flex items-center gap-2 py-2 border-t border-ink/10">
                    <div class="flex-1 min-w-0 text-[12.5px] truncate" x-text="productName(c.id)"></div>
                    <button @click="changeQty(c.id,-1)" class="w-[26px] h-[26px] border border-ink/15 bg-white rounded">−</button>
                    <input type="number" x-model.number="c.qty" @change="clampQty(c)" min="1"
                           class="w-12 px-1 py-1 border border-ink/15 rounded text-[12.5px] font-bold text-center">
                    <button @click="changeQty(c.id,1)" class="w-[26px] h-[26px] border border-ink/15 bg-white rounded">+</button>
                    <span class="text-[12.5px] font-bold w-[88px] text-right" x-text="money(lineTotal(c))"></span>
                  </div>
                </template>
                <div class="flex items-center gap-2 py-2.5 border-t border-ink/15 mt-1">
                  <span class="text-[11.5px] font-semibold text-ink/60 shrink-0">Chegirma/Ustama</span>
                  <input type="number" x-model.number="cartPct"
                         class="w-16 px-1.5 py-1.5 border border-ink/15 rounded text-[12.5px] text-center">
                  <span class="text-[11px] text-ink/50 shrink-0">%</span>
                </div>
                <div class="flex items-center justify-between py-1">
                  <span class="text-[13px] font-semibold text-ink/60">Jami</span>
                  <span class="text-[19px] font-extrabold" x-text="money(cartTotal)"></span>
                </div>
                <div class="flex gap-1.5 my-2">
                  <template x-for="t in ['Naqd','Karta','Nasiya']" :key="t">
                    <button @click="payType = t"
                      class="px-3 py-1.5 rounded border text-xs font-semibold"
                      :class="payType === t ? 'bg-accent text-[#FAFAF7] border-accent' : 'border-ink/15 text-ink/65'"
                      x-text="t"></button>
                  </template>
                </div>
                <div x-show="payType === 'Nasiya'" class="flex gap-1.5 mb-2">
                  <select x-model="checkoutCustomerId"
                          class="flex-1 min-w-0 px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px]">
                    <option :value="null" disabled>Mijozni tanlang</option>
                    <template x-for="c in customers" :key="c.id">
                      <option :value="c.id" x-text="c.name"></option>
                    </template>
                  </select>
                  <button @click="openNewCustomer()" class="shrink-0 px-3 rounded-md border border-accent text-accent text-xs font-bold">+ Yangi</button>
                </div>
                <button @click="checkout()" class="w-full p-3 rounded-md bg-accent text-[#FAFAF7] text-sm font-bold min-h-[44px]">Sotuvni yakunlash</button>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- Mobil: savat xulosasi (suzib turadigan panel) -->
      <button x-show="nav === 'sotuv' && cart.length > 0 && !cartSheetOpen" x-cloak @click="cartSheetOpen = true"
              class="lg:hidden fixed left-4 right-4 z-[45] flex items-center justify-between px-4 py-3 rounded-full bg-ink shadow-lg"
              style="bottom:calc(70px + env(safe-area-inset-bottom));">
        <span class="flex items-center gap-2 text-[13px] font-bold text-[#FAFAF7]">
          <i data-lucide="shopping-cart" class="w-4 h-4"></i>
          <span x-text="cartCount + ' mahsulot'"></span>
        </span>
        <span class="text-[15px] font-extrabold text-[#FAFAF7]" x-text="money(cartTotal)"></span>
      </button>

      <!-- Mobil: savat pastdan chiqadigan varaq -->
      <div x-show="cartSheetOpen" x-cloak class="lg:hidden fixed inset-0 z-[65] bg-ink/40 flex items-end justify-center" @click.self="cartSheetOpen = false">
        <div class="safe-bottom bg-white rounded-t-lg p-3.5 w-full max-h-[85vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
          <div class="flex justify-center -mt-1 mb-2"><div class="w-9 h-1 rounded-full bg-ink/15"></div></div>
          <div class="flex items-center justify-between mb-2">
            <div class="text-[13.5px] font-bold" x-text="'Savat (' + cartCount + ')'"></div>
            <button @click="cartSheetOpen = false" class="w-7 h-7 flex items-center justify-center text-ink/50">
              <i data-lucide="x" class="w-4 h-4"></i>
            </button>
          </div>
          <template x-if="cart.length === 0">
            <div class="text-[12.5px] text-ink/45 py-4 text-center">Savat bo'sh</div>
          </template>
          <template x-if="cart.length > 0">
            <div>
              <template x-for="c in cart" :key="c.id">
                <div class="flex items-center gap-2 py-2 border-t border-ink/10">
                  <div class="flex-1 min-w-0 text-[12.5px] truncate" x-text="productName(c.id)"></div>
                  <button @click="changeQty(c.id,-1)" class="w-[26px] h-[26px] border border-ink/15 bg-white rounded">−</button>
                  <input type="number" x-model.number="c.qty" @change="clampQty(c)" min="1"
                         class="w-12 px-1 py-1 border border-ink/15 rounded text-[12.5px] font-bold text-center">
                  <button @click="changeQty(c.id,1)" class="w-[26px] h-[26px] border border-ink/15 bg-white rounded">+</button>
                  <span class="text-[12.5px] font-bold w-[88px] text-right" x-text="money(lineTotal(c))"></span>
                </div>
              </template>
              <div class="flex items-center gap-2 py-2.5 border-t border-ink/15 mt-1">
                <span class="text-[11.5px] font-semibold text-ink/60 shrink-0">Chegirma/Ustama</span>
                <input type="number" x-model.number="cartPct"
                       class="w-16 px-1.5 py-1.5 border border-ink/15 rounded text-[12.5px] text-center">
                <span class="text-[11px] text-ink/50 shrink-0">%</span>
              </div>
              <div class="flex items-center justify-between py-1">
                <span class="text-[13px] font-semibold text-ink/60">Jami</span>
                <span class="text-[19px] font-extrabold" x-text="money(cartTotal)"></span>
              </div>
              <div class="flex gap-1.5 my-2">
                <template x-for="t in ['Naqd','Karta','Nasiya']" :key="t">
                  <button @click="payType = t"
                    class="px-3 py-1.5 rounded border text-xs font-semibold"
                    :class="payType === t ? 'bg-accent text-[#FAFAF7] border-accent' : 'border-ink/15 text-ink/65'"
                    x-text="t"></button>
                </template>
              </div>
              <div x-show="payType === 'Nasiya'" class="flex gap-1.5 mb-2">
                <select x-model="checkoutCustomerId"
                        class="flex-1 min-w-0 px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px]">
                  <option :value="null" disabled>Mijozni tanlang</option>
                  <template x-for="c in customers" :key="c.id">
                    <option :value="c.id" x-text="c.name"></option>
                  </template>
                </select>
                <button @click="openNewCustomer()" class="shrink-0 px-3 rounded-md border border-accent text-accent text-xs font-bold">+ Yangi</button>
              </div>
              <button @click="checkout()" class="w-full p-3 rounded-md bg-accent text-[#FAFAF7] text-sm font-bold min-h-[44px]">Sotuvni yakunlash</button>
            </div>
          </template>
        </div>
      </div>

      <!-- ============ MAHSULOTLAR ============ -->
      <div x-show="nav === 'mahsulotlar'" x-cloak>
        <div class="flex items-center justify-end mb-2" x-show="role === 'admin'">
          <button @click="openNewProduct()" class="text-[11.5px] font-bold text-accent border border-accent rounded px-2.5 py-1.5">+ Yangi mahsulot</button>
        </div>
        <input type="text" x-model="prodSearch" placeholder="Mahsulot qidirish..."
               class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px]">
        <div class="flex gap-1.5 overflow-x-auto my-2 pb-0.5">
          <template x-for="c in saleCategories" :key="c">
            <button @click="prodCategory = c"
              class="px-3 py-1.5 rounded border text-xs font-semibold whitespace-nowrap"
              :class="prodCategory === c ? 'bg-accent text-[#FAFAF7] border-accent' : 'border-ink/15 text-ink/65'"
              x-text="c + ' (' + categoryCount(c) + ')'"></button>
          </template>
        </div>
        <div class="bg-white border border-ink/10 rounded-md p-3.5">
          <template x-if="filteredProdList.length === 0">
            <div class="flex flex-col items-center gap-2 py-8 px-4">
              <i data-lucide="search-x" class="w-6 h-6 text-ink/30"></i>
              <span class="text-[12.5px] text-ink/50 text-center">Mahsulot topilmadi</span>
            </div>
          </template>
          <template x-for="p in filteredProdList" :key="p.id">
            <div class="flex items-center gap-2.5 py-2.5 border-t border-ink/10 first:border-t-0">
              <button @click="openZoom(p)" :disabled="!p.image_url"
                      class="w-14 h-14 shrink-0 bg-[#F1EFEA] rounded overflow-hidden flex items-center justify-center">
                <img :src="p.image_url" x-show="p.image_url" class="w-full h-full object-cover">
                <i :data-lucide="p.icon" x-show="!p.image_url" class="w-6 h-6 text-ink/45"></i>
              </button>
              <button @click="openZoom(p)" class="flex-1 min-w-0 text-left">
                <div class="text-[13px] font-semibold truncate" x-text="p.name"></div>
                <div class="text-[11px] text-ink/50" x-text="p.category"></div>
              </button>
              <div class="text-[13px] font-bold shrink-0" x-text="money(p.price)"></div>
              <button @click="deleteProduct(p)" x-show="role === 'admin'"
                      class="shrink-0 w-8 h-8 flex items-center justify-center text-red-700/70 rounded hover:bg-red-700/10">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </div>
          </template>
        </div>
      </div>

      <!-- ============ MIJOZLAR ============ -->
      <div x-show="nav === 'mijozlar'" x-cloak>
        <div class="flex items-center justify-end mb-2">
          <button @click="openNewCustomer()" class="text-[11.5px] font-bold text-accent border border-accent rounded px-2.5 py-1.5">+ Yangi mijoz</button>
        </div>
        <div class="bg-white border border-ink/10 rounded-md p-3.5">
          <template x-for="c in customers" :key="c.id">
            <button @click="selectedCustomerId = c.id; settleAmount = c.debt" class="flex items-center gap-2.5 py-2.5 border-t border-ink/10 w-full text-left">
              <div class="w-9 h-9 shrink-0 bg-[#F1EFEA] rounded flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-ink/45"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-[13px] font-semibold" x-text="c.name"></div>
                <div class="text-[11px] text-ink/50" x-text="'Oxirgi to\'lov: ' + uzDate(c.last_payment_at)"></div>
              </div>
              <div class="text-sm font-bold" :class="c.debt > 0 ? 'text-[#8A3D1F]' : 'text-emerald-700'" x-text="money(c.debt)"></div>
            </button>
          </template>
        </div>
      </div>

      <!-- ============ HISOBOT ============ -->
      <div x-show="nav === 'hisobot'" x-cloak>
        <div class="flex gap-1.5 mb-2.5">
          <template x-for="p in [{id:'bugun',label:'Bugun'},{id:'hafta',label:'Hafta'},{id:'oy',label:'Oy'}]" :key="p.id">
            <button @click="hisobotPeriod = p.id"
              class="px-3 py-1.5 rounded border text-xs font-semibold"
              :class="hisobotPeriod === p.id ? 'bg-accent text-[#FAFAF7] border-accent' : 'border-ink/15 text-ink/65'"
              x-text="p.label"></button>
          </template>
        </div>
        <div class="grid grid-cols-2 gap-2.5 mb-2.5">
          <div class="bg-white border border-ink/10 rounded-md p-2.5">
            <span class="text-[10px] font-medium text-ink/60">Savdo</span>
            <div class="text-[17px] font-bold mt-1.5" x-text="money(periodMetrics.savdo)"></div>
          </div>
          <div class="bg-white border border-ink/10 rounded-md p-2.5">
            <span class="text-[10px] font-medium text-ink/60">Sof foyda</span>
            <div class="text-[17px] font-bold mt-1.5" x-text="money(periodMetrics.foyda)"></div>
          </div>
          <div class="bg-white border border-ink/10 rounded-md p-2.5">
            <span class="text-[10px] font-medium text-ink/60">Cheklar soni</span>
            <div class="text-[17px] font-bold mt-1.5" x-text="periodMetrics.cheklar"></div>
          </div>
          <div class="bg-white border border-ink/10 rounded-md p-2.5">
            <span class="text-[10px] font-medium text-ink/60">O'rtacha chek</span>
            <div class="text-[17px] font-bold mt-1.5" x-text="money(periodMetrics.ortacha)"></div>
          </div>
        </div>
        <div class="bg-white border border-ink/10 rounded-md p-3.5">
          <div class="text-[13.5px] font-bold mb-1.5">Kategoriya bo'yicha savdo</div>
          <template x-for="c in categoryBreakdown" :key="c.label">
            <div class="my-2.5">
              <div class="flex justify-between text-xs mb-1">
                <span class="font-medium" x-text="c.label"></span>
                <span class="text-ink/50 font-semibold" x-text="c.pct + '%'"></span>
              </div>
              <div class="h-1.5 bg-ink/10 rounded">
                <div class="h-full bg-accent rounded" :style="`width:${c.pct}%`"></div>
              </div>
            </div>
          </template>
        </div>
        <div class="bg-white border border-ink/10 rounded-md p-3.5 mt-2.5">
          <div class="text-[13.5px] font-bold mb-1">API token (Excel import uchun)</div>
          <div class="text-[11px] text-ink/50 mb-2.5">Bu tokenni Excel makrosiga qo'ying — shunda mahsulotlarni tugma orqali avtomatik yuklashingiz mumkin.</div>
          <template x-if="apiToken">
            <div class="flex gap-1.5 mb-2.5">
              <input type="text" readonly :value="apiToken" @click="$event.target.select()"
                     class="flex-1 min-w-0 px-3 py-2.5 border border-ink/10 rounded-md bg-[#F1EFEA] text-[11px] font-mono">
              <button @click="copyApiToken()" class="px-3 rounded-md border border-ink/15 text-[11.5px] font-bold shrink-0">Nusxalash</button>
            </div>
          </template>
          <template x-if="!apiToken">
            <div class="text-[12px] text-ink/50 mb-2.5">Hali token yaratilmagan.</div>
          </template>
          <button @click="regenerateApiToken()" class="text-[11.5px] font-bold text-accent border border-accent rounded px-2.5 py-1.5">
            <span x-text="apiToken ? 'Tokenni yangilash' : 'Token yaratish'"></span>
          </button>
        </div>

        <div class="bg-white border border-ink/10 rounded-md p-3.5 mt-2.5">
          <div class="flex items-center gap-2 mb-1">
            <i data-lucide="percent" class="w-4 h-4 text-accent"></i>
            <div class="text-[13.5px] font-bold">Barcha narxlarni o'zgartirish</div>
          </div>
          <div class="text-[11px] text-ink/50 mb-2.5">
            Barcha mahsulotlarning sotuv narxi shu foizga oshiriladi (kamaytirish uchun manfiy son kiriting, masalan -10).
          </div>
          <div class="flex items-center gap-2">
            <div class="relative flex-1 min-w-0">
              <input type="number" step="0.1" x-model.number="bulkPricePercent" placeholder="Masalan: 10"
                     class="w-full pl-3 pr-7 py-2.5 border border-ink/10 rounded-md bg-white text-[13px]">
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[12px] text-ink/40 font-semibold">%</span>
            </div>
            <button @click="applyBulkPriceUpdate()" :disabled="bulkPriceApplying"
                    class="shrink-0 px-4 py-2.5 rounded-md bg-accent text-[#FAFAF7] text-[13px] font-bold disabled:opacity-50">
              <span x-show="!bulkPriceApplying">Qo'llash</span>
              <span x-show="bulkPriceApplying" x-cloak>Bajarilmoqda...</span>
            </button>
          </div>
        </div>
      </div>

      <div class="h-24"></div>
    </div>
  </div>

  <!-- Toast -->
  <div x-show="toast" x-cloak x-transition
       class="fixed left-1/2 -translate-x-1/2 z-50 bg-ink text-[#FAFAF7] px-4.5 py-2.5 rounded-md text-[13px] font-semibold max-w-[90vw] text-center bottom-[calc(96px+env(safe-area-inset-bottom))] lg:bottom-6"
       x-text="toast"></div>

  <!-- Receipt modal -->
  <div x-show="receiptOpen" x-cloak class="fixed inset-0 z-[70] bg-ink/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-[340px] max-h-[92vh] overflow-y-auto flex flex-col"
         @click.outside="receiptOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
      <div id="receipt-print" class="p-4 font-mono">
        <template x-if="receipt">
          <div>
            <div class="text-center mb-2.5">
              <div class="text-base font-extrabold">Kamera 24</div>
              <div class="text-[11px] text-ink/50">Mobil aksessuarlar</div>
            </div>
            <div class="border-t border-dashed border-ink/30 my-2.5"></div>
            <div class="flex justify-between text-[12px] py-0.5"><span class="text-ink/55">Chek raqami</span><span class="font-bold">№ <span x-text="receipt.id"></span></span></div>
            <div class="flex justify-between text-[12px] py-0.5"><span class="text-ink/55">Sana</span><span class="font-semibold" x-text="receiptDate(receipt.created_at)"></span></div>
            <div class="flex justify-between text-[12px] py-0.5"><span class="text-ink/55">Kassir</span><span class="font-semibold" x-text="receipt.cashier"></span></div>
            <template x-if="receipt.customer">
              <div class="flex justify-between text-[12px] py-0.5"><span class="text-ink/55">Mijoz</span><span class="font-semibold" x-text="receipt.customer"></span></div>
            </template>
            <div class="border-t border-dashed border-ink/30 my-2.5"></div>
            <template x-for="item in receipt.items" :key="item.name + item.qty + item.unit_price">
              <div class="mb-2">
                <div class="text-[13px] font-bold" x-text="item.name"></div>
                <div class="flex justify-between text-[12px] text-ink/55 mt-0.5">
                  <span x-text="item.qty + ' dona × ' + money(item.unit_price)"></span>
                  <span class="font-bold text-ink" x-text="money(item.line_total)"></span>
                </div>
              </div>
            </template>
            <div class="border-t border-dashed border-ink/30 my-2.5"></div>
            <div class="flex justify-between text-[12px] py-0.5"><span class="text-ink/55">Mahsulotlar soni</span><span class="font-semibold" x-text="receiptItemCount + ' dona'"></span></div>
            <div class="bg-ink/5 rounded-md px-3 py-2.5 mt-2 flex items-center justify-between">
              <span class="text-[13px] font-bold text-ink/70">Jami to'lov</span>
              <span class="text-[20px] font-extrabold" x-text="money(receipt.total)"></span>
            </div>
            <div class="flex justify-between items-center text-[12px] mt-2.5">
              <span class="text-ink/55">To'lov turi</span>
              <span class="text-[11px] font-bold px-2 py-1 rounded" :class="payBadge(receipt.pay_type)" x-text="receipt.pay_type"></span>
            </div>
            <div class="border-t border-dashed border-ink/30 my-2.5"></div>
            <div class="text-center text-[11px] text-ink/40">Xaridingiz uchun rahmat!</div>
          </div>
        </template>
      </div>
      <div class="safe-bottom flex gap-2 p-3 border-t border-ink/10">
        <button @click="receiptOpen = false" class="flex-1 p-2.5 border border-ink/15 bg-white rounded-md text-[13px] font-semibold">Yopish</button>
        <button @click="downloadReceipt()" class="flex-1 p-2.5 rounded-md bg-accent text-[#FAFAF7] text-[13px] font-bold">PDF yuklab olish</button>
      </div>
    </div>
  </div>

  <!-- New customer modal -->
  <div x-show="newCustomerOpen" x-cloak class="fixed inset-0 z-[60] bg-ink/40 flex items-end lg:items-center justify-center lg:p-4">
    <div class="safe-bottom bg-white rounded-t-lg lg:rounded-lg p-6 w-full max-w-[360px]"
         @click.outside="newCustomerOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full lg:translate-y-4 lg:opacity-0"
         x-transition:enter-end="translate-y-0 lg:opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 lg:opacity-100"
         x-transition:leave-end="translate-y-full lg:translate-y-4 lg:opacity-0">
      <div class="lg:hidden flex justify-center -mt-2 mb-2"><div class="w-9 h-1 rounded-full bg-ink/15"></div></div>
      <div class="text-sm font-bold mb-3">Yangi mijoz qo'shish</div>
      <label class="text-[11.5px] text-ink/60 font-semibold">Ism-familiya</label>
      <input type="text" x-model="newCustomer.name" class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] mt-1.5 mb-3.5">
      <label class="text-[11.5px] text-ink/60 font-semibold">Telefon (ixtiyoriy)</label>
      <input type="text" x-model="newCustomer.phone" class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] my-1 mb-3.5">
      <div class="flex gap-2">
        <button @click="newCustomerOpen = false" class="flex-1 p-2.5 border border-ink/15 bg-white rounded-md text-[13px] font-semibold">Bekor qilish</button>
        <button @click="saveNewCustomer()" class="flex-1 p-2.5 rounded-md bg-accent text-[#FAFAF7] text-[13px] font-bold">Saqlash</button>
      </div>
    </div>
  </div>

  <!-- New product modal -->
  <div x-show="newProductOpen" x-cloak class="fixed inset-0 z-[60] bg-ink/40 flex items-end lg:items-center justify-center lg:p-4">
    <div class="safe-bottom bg-white rounded-t-lg lg:rounded-lg p-6 w-full max-w-[360px] max-h-[90vh] overflow-y-auto"
         @click.outside="newProductOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full lg:translate-y-4 lg:opacity-0"
         x-transition:enter-end="translate-y-0 lg:opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 lg:opacity-100"
         x-transition:leave-end="translate-y-full lg:translate-y-4 lg:opacity-0">
      <div class="lg:hidden flex justify-center -mt-2 mb-2"><div class="w-9 h-1 rounded-full bg-ink/15"></div></div>
      <div class="text-sm font-bold mb-3">Yangi mahsulot qo'shish</div>
      <label class="text-[11.5px] text-ink/60 font-semibold">Nomi</label>
      <input type="text" x-model="newProduct.name" class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] mt-1.5 mb-3.5">
      <label class="text-[11.5px] text-ink/60 font-semibold">Kategoriya</label>
      <input type="text" x-model="newProduct.category" class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] mt-1.5 mb-3.5">
      <label class="text-[11.5px] text-ink/60 font-semibold">Sotuv narxi ($)</label>
      <input type="number" step="0.01" x-model="newProduct.price" class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] mt-1.5 mb-3.5">
      <label class="text-[11.5px] text-ink/60 font-semibold">Tannarx ($)</label>
      <input type="number" step="0.01" x-model="newProduct.cost" class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] mt-1.5 mb-3.5">
      <label class="text-[11.5px] text-ink/60 font-semibold">Rasm (ixtiyoriy)</label>
      <div class="my-1 mb-3.5">
        <input type="file" accept="image/*" @change="onNewProductImageSelected($event)" id="productImageInput" class="hidden">
        <template x-if="!newProduct.image_base64">
          <label for="productImageInput" class="flex flex-col items-center justify-center gap-1.5 w-28 h-28 rounded-md border-2 border-dashed border-ink/15 text-ink/40 cursor-pointer active:bg-ink/5">
            <i data-lucide="camera" class="w-6 h-6"></i>
            <span class="text-[10.5px] font-semibold">Rasm tanlash</span>
          </label>
        </template>
        <template x-if="newProduct.image_base64">
          <div class="relative w-28 h-28">
            <img :src="newProduct.image_base64" class="w-full h-full object-cover rounded-md border border-ink/10">
            <button type="button" @click="newProduct.image_base64 = ''"
                    class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-ink text-[#FAFAF7] flex items-center justify-center shadow">
              <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
          </div>
        </template>
      </div>
      <div class="flex gap-2">
        <button @click="newProductOpen = false" class="flex-1 p-2.5 border border-ink/15 bg-white rounded-md text-[13px] font-semibold">Bekor qilish</button>
        <button @click="saveNewProduct()" class="flex-1 p-2.5 rounded-md bg-accent text-[#FAFAF7] text-[13px] font-bold">Saqlash</button>
      </div>
    </div>
  </div>

  <!-- Customer modal -->
  <div x-show="selectedCustomerId !== null" x-cloak class="fixed inset-0 z-[60] bg-ink/40 flex items-end lg:items-center justify-center lg:p-4">
    <div class="safe-bottom bg-white rounded-t-lg lg:rounded-lg p-6 w-full max-w-[360px]"
         @click.outside="selectedCustomerId = null" x-show="selectedCustomer"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full lg:translate-y-4 lg:opacity-0"
         x-transition:enter-end="translate-y-0 lg:opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 lg:opacity-100"
         x-transition:leave-end="translate-y-full lg:translate-y-4 lg:opacity-0">
      <div class="lg:hidden flex justify-center -mt-2 mb-2"><div class="w-9 h-1 rounded-full bg-ink/15"></div></div>
      <div class="text-[15px] font-bold" x-text="selectedCustomer?.name"></div>
      <div class="text-xs text-ink/50 mt-0.5" x-text="selectedCustomer?.phone"></div>
      <div class="mt-3.5 p-3.5 bg-[#FBEEE7] rounded-md">
        <div class="text-[11.5px] font-semibold text-[#8A3D1F]">Nasiya qarzi</div>
        <div class="text-2xl font-extrabold text-[#8A3D1F] mt-0.5" x-text="selectedCustomer ? money(selectedCustomer.debt) : ''"></div>
      </div>
      <template x-if="selectedCustomer && selectedCustomer.debt > 0">
        <div class="mt-3">
          <label class="text-[11.5px] text-ink/60 font-semibold">To'lanadigan summa ($)</label>
          <input type="number" step="0.01" x-model.number="settleAmount"
                 class="w-full px-3 py-2.5 border border-ink/10 rounded-md bg-white text-[13px] my-1">
        </div>
      </template>
      <div class="flex gap-2 mt-3.5">
        <button @click="selectedCustomerId = null" class="flex-1 p-2.5 border border-ink/15 bg-white rounded-md text-[13px] font-semibold">Yopish</button>
        <button @click="settleCustomer()" x-show="selectedCustomer && selectedCustomer.debt > 0" class="flex-1 p-2.5 rounded-md bg-accent text-[#FAFAF7] text-[13px] font-bold">To'lov qabul qilish</button>
      </div>
    </div>
  </div>

  <!-- Image zoom modal -->
  <div x-show="zoomImage" x-cloak x-transition class="fixed inset-0 z-[80] bg-ink/85 flex items-center justify-center p-4" @click="zoomImage = null">
    <button @click="zoomImage = null" class="safe-top absolute top-4 right-4 w-10 h-10 rounded-full bg-white/90 flex items-center justify-center">
      <i data-lucide="x" class="w-5 h-5 text-ink"></i>
    </button>
    <div class="max-w-[92vw] max-h-[85vh] flex flex-col items-center gap-3" @click.stop>
      <img :src="zoomImage" class="max-w-full max-h-[75vh] object-contain rounded-lg bg-white">
      <div class="text-[13px] font-semibold text-[#FAFAF7] text-center" x-text="zoomName"></div>
    </div>
  </div>

  <!-- Mobile bottom nav + FAB -->
  <div class="safe-bottom lg:hidden fixed bottom-0 left-0 right-0 z-30 flex bg-[#FAFAF7]/85 backdrop-blur-md border-t border-ink/10">
    <template x-for="item in navItems" :key="item.id">
      <button @click="nav = item.id" class="flex flex-col items-center gap-0.5 flex-1 py-1.5 min-h-[44px] justify-center">
        <i :data-lucide="item.icon" class="w-5 h-5" :class="nav === item.id ? 'text-accent' : 'text-ink/45'"></i>
        <span class="text-[10px]" :class="nav === item.id ? 'font-bold text-accent' : 'font-medium text-ink/45'" x-text="item.label"></span>
      </button>
    </template>
  </div>
  <button @click="nav = 'sotuv'" x-show="nav !== 'sotuv'" x-cloak
          class="lg:hidden fixed z-[31] w-14 h-14 rounded-full bg-accent shadow-lg flex items-center justify-center"
          style="right:16px; bottom:calc(70px + env(safe-area-inset-bottom));">
    <i data-lucide="plus" class="w-6 h-6 text-[#FAFAF7]"></i>
  </button>

  <script>
    async function api(url, opts = {}) {
      const res = await fetch(url, {
        ...opts,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          ...(opts.headers || {}),
        },
      });
      if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw new Error(body.message || 'Xatolik yuz berdi');
      }
      return res.status === 204 ? null : res.json();
    }

    function dashboard(initial = {}) {
      return {
        role: initial.role || 'sotuvchi',
        nav: 'dashboard',
        offline: !navigator.onLine,
        salesState: 'loading',
        saleSearch: '', saleCategory: 'Barchasi',
        prodSearch: '', prodCategory: 'Barchasi',
        payType: 'Naqd',
        checkoutCustomerId: null,
        cart: [],
        cartPct: 0,
        toast: null,
        newProductOpen: false,
        newProduct: { name: '', category: '', price: '', cost: '', image_base64: '' },
        newCustomerOpen: false,
        newCustomer: { name: '', phone: '' },
        receiptOpen: false, receipt: null,
        selectedCustomerId: null,
        settleAmount: 0,
        hisobotPeriod: 'bugun',
        cartSheetOpen: false,
        zoomImage: null, zoomName: '',
        bulkPricePercent: '', bulkPriceApplying: false,

        products: [],
        customers: [],
        recentSales: [],
        chartData: [],
        categoryBreakdown: [],
        reportData: { savdo: 0, foyda: 0, cheklar: 0, ortacha: 0 },
        apiToken: null,
        _apiTokenLoaded: false,

        salesToday: 0, salesYesterday: 0,
        profitToday: 0, profitYesterday: 0,
        receiptsToday: 0, totalDebt: 0,

        allNavItems: [
          { id: 'dashboard', icon: 'layout-dashboard', label: 'Dashboard' },
          { id: 'sotuv', icon: 'shopping-cart', label: 'Sotuv' },
          { id: 'mahsulotlar', icon: 'package', label: 'Mahsulotlar' },
          { id: 'mijozlar', icon: 'users', label: 'Mijozlar' },
          { id: 'hisobot', icon: 'bar-chart-3', label: 'Hisobot' },
        ],

        async init() {
          this.$watch('nav', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
          this.$nextTick(() => window.lucide && window.lucide.createIcons());
          setInterval(() => window.lucide && window.lucide.createIcons(), 400);

          window.addEventListener('online', () => this.offline = false);
          window.addEventListener('offline', () => this.offline = true);

          if (this.role !== 'admin' && this.nav === 'hisobot') this.nav = 'dashboard';

          this.$watch('nav', (v) => {
            if (v === 'hisobot' && this.role === 'admin') {
              this.loadReports();
              if (!this._apiTokenLoaded) this.loadApiToken();
            }
          });
          this.$watch('hisobotPeriod', () => { if (this.role === 'admin') this.loadReports(); });

          await Promise.all([this.loadDashboard(), this.loadProducts(), this.loadCustomers()]);
        },

        get navItems() {
          return this.role === 'admin' ? this.allNavItems : this.allNavItems.filter(i => i.id !== 'hisobot');
        },

        async loadDashboard() {
          this.salesState = 'loading';
          try {
            const d = await api('/api/dashboard');
            this.salesToday = d.salesToday;
            this.salesYesterday = d.salesYesterday;
            this.receiptsToday = d.receiptsToday;
            this.totalDebt = d.totalDebt;
            this.chartData = d.chartData;
            this.recentSales = d.recentSales;
            this.salesState = d.salesState;
            if (this.role === 'admin') {
              this.profitToday = d.profitToday;
              this.profitYesterday = d.profitYesterday;
            }
          } catch (e) {
            this.salesState = 'empty';
            this.showToast(e.message);
          }
        },
        async loadProducts() {
          try { this.products = await api('/api/products'); }
          catch (e) { this.showToast(e.message); }
        },
        async loadCustomers() {
          try { this.customers = await api('/api/customers'); }
          catch (e) { this.showToast(e.message); }
        },
        async loadReports() {
          try {
            const d = await api('/api/reports?period=' + this.hisobotPeriod);
            this.reportData = { savdo: d.savdo, foyda: d.foyda, cheklar: d.cheklar, ortacha: d.ortacha };
            this.categoryBreakdown = d.categoryBreakdown;
          } catch (e) { this.showToast(e.message); }
        },
        async loadApiToken() {
          try {
            const d = await api('/api/api-token');
            this.apiToken = d.api_token;
            this._apiTokenLoaded = true;
          } catch (e) { this.showToast(e.message); }
        },
        async regenerateApiToken() {
          if (this.apiToken && !confirm("Eskisi ishlamay qoladi. Yangi token yaratilsinmi?")) return;
          try {
            const res = await api('/api/api-token/regenerate', { method: 'POST' });
            this.apiToken = res.api_token;
            this.showToast(res.message);
          } catch (e) { this.showToast(e.message); }
        },
        copyApiToken() {
          if (!this.apiToken) return;
          navigator.clipboard?.writeText(this.apiToken);
          this.showToast('Nusxalandi');
        },
        openZoom(p) {
          if (!p.image_url) return;
          this.zoomImage = p.image_url;
          this.zoomName = p.name;
        },
        async applyBulkPriceUpdate() {
          const percent = Number(this.bulkPricePercent);
          if (!percent) {
            this.showToast("Foiz qiymatini kiriting");
            return;
          }
          const dir = percent > 0 ? 'oshiriladi' : 'kamaytiriladi';
          if (!confirm(`Barcha mahsulotlarning narxi ${Math.abs(percent)}% ga ${dir}. Davom etasizmi?`)) return;
          this.bulkPriceApplying = true;
          try {
            const res = await api('/api/products/bulk-price-update', {
              method: 'POST',
              body: JSON.stringify({ percent }),
            });
            this.showToast(res.message);
            this.bulkPricePercent = '';
            await this.loadProducts();
          } catch (e) {
            this.showToast(e.message);
          } finally {
            this.bulkPriceApplying = false;
          }
        },
        fmt(n) {
          return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        money(n) { return '$' + this.fmt(n); },
        uzDate(iso) {
          if (!iso) return '—';
          const months = ['yan','fev','mar','apr','may','iyun','iyul','avg','sen','okt','noy','dek'];
          const d = new Date(iso);
          return `${d.getDate()}-${months[d.getMonth()]}`;
        },
        receiptDate(iso) {
          if (!iso) return '—';
          const d = new Date(iso);
          const hh = String(d.getHours()).padStart(2, '0');
          const mm = String(d.getMinutes()).padStart(2, '0');
          return `${this.uzDate(iso)} ${d.getFullYear()}, ${hh}:${mm}`;
        },
        async viewReceipt(saleId) {
          try {
            const res = await api('/api/sales/' + saleId);
            this.receipt = res.sale;
            this.receiptOpen = true;
          } catch (e) {
            this.showToast(e.message);
          }
        },
        async downloadReceipt() {
          if (!this.receipt) return;
          try {
            const el = document.getElementById('receipt-print');
            const canvas = await html2canvas(el, { scale: 3, backgroundColor: '#ffffff' });
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdfWidth = 80;
            const pdfHeight = pdfWidth * canvas.height / canvas.width;
            const doc = new jsPDF({
              unit: 'mm',
              format: [pdfWidth, pdfHeight],
              orientation: pdfHeight >= pdfWidth ? 'p' : 'l',
            });
            doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            doc.save(`chek-${this.receipt.id}.pdf`);
          } catch (e) {
            this.showToast("PDF yaratishda xatolik yuz berdi");
          }
        },
        payBadge(t) {
          return t === 'Naqd' ? 'text-emerald-700 bg-emerald-700/10'
            : t === 'Karta' ? 'text-blue-700 bg-blue-700/10'
            : 'text-accent bg-accent/10';
        },
        pctChange(now, prev) {
          if (!prev) return null;
          return Math.round(((now - prev) / prev) * 100);
        },
        get salesChangePct() { return this.pctChange(this.salesToday, this.salesYesterday); },
        get profitChangePct() { return this.pctChange(this.profitToday, this.profitYesterday); },
        get chartMax() { return Math.max(1, ...this.chartData.map(d => d.v)); },
        get saleCategories() { return ['Barchasi', ...new Set(this.products.map(p => p.category))]; },
        categoryCount(c) {
          return c === 'Barchasi' ? this.products.length : this.products.filter(p => p.category === c).length;
        },
        get filteredSaleProducts() {
          return this.products.filter(p =>
            (this.saleCategory === 'Barchasi' || p.category === this.saleCategory) &&
            p.name.toLowerCase().includes(this.saleSearch.toLowerCase()));
        },
        get filteredProdList() {
          return this.products.filter(p =>
            (this.prodCategory === 'Barchasi' || p.category === this.prodCategory) &&
            p.name.toLowerCase().includes(this.prodSearch.toLowerCase()));
        },
        productName(id) {
          const p = this.products.find(pr => pr.id === id);
          return p ? p.name : '';
        },
        productPrice(id) {
          const p = this.products.find(pr => pr.id === id);
          return p ? p.price : 0;
        },
        linePrice(c) {
          const base = this.productPrice(c.id);
          return Math.round(base * (1 + (Number(this.cartPct) || 0) / 100) * 100) / 100;
        },
        lineTotal(c) {
          return this.linePrice(c) * c.qty;
        },
        get cartTotal() {
          return this.cart.reduce((sum, c) => sum + this.lineTotal(c), 0);
        },
        get cartCount() { return this.cart.reduce((sum, c) => sum + c.qty, 0); },
        get selectedCustomer() { return this.customers.find(c => c.id === this.selectedCustomerId); },
        get periodMetrics() { return this.reportData; },
        get receiptItemCount() {
          return this.receipt ? this.receipt.items.reduce((sum, i) => sum + i.qty, 0) : 0;
        },

        addToCart(p) {
          const existing = this.cart.find(c => c.id === p.id);
          if (existing) existing.qty++;
          else this.cart.push({ id: p.id, qty: 1 });
        },
        changeQty(id, delta) {
          const line = this.cart.find(c => c.id === id);
          if (!line) return;
          line.qty += delta;
          if (line.qty <= 0) this.cart = this.cart.filter(c => c.id !== id);
        },
        clampQty(c) {
          c.qty = Math.floor(Number(c.qty)) || 0;
          if (c.qty <= 0) this.cart = this.cart.filter(x => x.id !== c.id);
        },
        async checkout() {
          if (this.cart.length === 0) return;
          if (this.payType === 'Nasiya' && !this.checkoutCustomerId) {
            this.showToast("Mijozni tanlang");
            return;
          }
          try {
            const payload = {
              items: this.cart.map(c => ({ product_id: c.id, qty: c.qty, price: this.linePrice(c) })),
              pay_type: this.payType,
              customer_id: this.payType === 'Nasiya' ? this.checkoutCustomerId : null,
            };
            const res = await api('/api/sales', { method: 'POST', body: JSON.stringify(payload) });
            this.cart = [];
            this.cartPct = 0;
            this.checkoutCustomerId = null;
            this.cartSheetOpen = false;
            this.receipt = res.sale;
            this.receiptOpen = true;
            await Promise.all([this.loadDashboard(), this.loadProducts(), this.loadCustomers()]);
          } catch (e) {
            this.showToast(e.message);
          }
        },
        openNewProduct() {
          this.newProduct = { name: '', category: '', price: '', cost: '', image_base64: '' };
          this.newProductOpen = true;
        },
        onNewProductImageSelected(event) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = () => { this.newProduct.image_base64 = reader.result; };
          reader.readAsDataURL(file);
        },
        async saveNewProduct() {
          if (!this.newProduct.name || !this.newProduct.category || this.newProduct.price === '' || this.newProduct.cost === '') {
            this.showToast("Nomi, kategoriya, narx va tannarxni to'ldiring");
            return;
          }
          try {
            await api('/api/products', {
              method: 'POST',
              body: JSON.stringify({
                name: this.newProduct.name,
                category: this.newProduct.category,
                price: Number(this.newProduct.price),
                cost: Number(this.newProduct.cost),
                image_base64: this.newProduct.image_base64 || null,
              }),
            });
            this.newProductOpen = false;
            this.showToast("Mahsulot qo'shildi");
            await Promise.all([this.loadProducts(), this.loadDashboard()]);
          } catch (e) {
            this.showToast(e.message);
          }
        },
        async deleteProduct(p) {
          if (!confirm(`"${p.name}" mahsulotini o'chirmoqchimisiz?`)) return;
          try {
            const res = await api(`/api/products/${p.id}`, { method: 'DELETE' });
            this.showToast(res.message);
            await Promise.all([this.loadProducts(), this.loadDashboard()]);
          } catch (e) {
            this.showToast(e.message);
          }
        },
        openNewCustomer() {
          this.newCustomer = { name: '', phone: '' };
          this.newCustomerOpen = true;
        },
        async saveNewCustomer() {
          if (!this.newCustomer.name) {
            this.showToast("Mijoz ismini kiriting");
            return;
          }
          try {
            const res = await api('/api/customers', {
              method: 'POST',
              body: JSON.stringify({
                name: this.newCustomer.name,
                phone: this.newCustomer.phone || null,
              }),
            });
            this.newCustomerOpen = false;
            this.showToast(res.message);
            await this.loadCustomers();
            this.checkoutCustomerId = res.customer.id;
          } catch (e) {
            this.showToast(e.message);
          }
        },
        async settleCustomer() {
          if (!this.selectedCustomerId) return;
          const amount = Number(this.settleAmount) || 0;
          if (amount <= 0) {
            this.showToast("To'lov summasini kiriting");
            return;
          }
          try {
            const res = await api(`/api/customers/${this.selectedCustomerId}/settle`, {
              method: 'POST',
              body: JSON.stringify({ amount }),
            });
            this.selectedCustomerId = null;
            this.showToast(res.message);
            await this.loadCustomers();
          } catch (e) {
            this.showToast(e.message);
          }
        },
        showToast(msg) {
          this.toast = msg;
          setTimeout(() => { this.toast = null; }, 2000);
        },
      };
    }
  </script>
</body>
</html>
