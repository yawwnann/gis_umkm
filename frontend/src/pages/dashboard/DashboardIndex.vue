<template>
  <div class="w-full flex flex-col font-sans">

    <!-- Top Grid: existing 3 panels -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-6 shrink-0">

      <!-- Panel 1: Data Overview -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl flex flex-col justify-between pointer-events-auto">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-xl font-bold flex items-center space-x-2 text-slate-800 dark:text-white">
            <div class="h-6 w-6 bg-[#F59E0B]/20 rounded flex items-center justify-center">
              <Store class="h-4 w-4 text-[#F59E0B]" />
            </div>
            <span>Data Overview</span>
          </h3>
          <div class="flex items-center space-x-1.5">
            <RouterLink to="/dashboard/by-village" class="p-1.5 hover:bg-slate-100 dark:hover:bg-[#22262A] rounded-lg transition-colors text-slate-400 dark:text-[#6F767E]" title="Lihat Statistik">
              <SlidersHorizontal class="h-3.5 w-3.5" />
            </RouterLink>
            <RouterLink to="/dashboard/by-category" class="p-1.5 hover:bg-slate-100 dark:hover:bg-[#22262A] rounded-lg transition-colors text-slate-400 dark:text-[#6F767E]" title="Lihat Kategori">
              <CalendarDays class="h-3.5 w-3.5" />
            </RouterLink>
          </div>
        </div>

        <div>
          <div class="mb-1">
            <span class="text-[36px] font-extrabold text-slate-900 dark:text-white leading-none">{{ loading ? '...' : stats?.total_umkm }}</span>
            <span class="text-[36px] font-extrabold text-slate-300 dark:text-[#6F767E]">.{{ loading ? '' : '00' }}</span>
          </div>

          <div class="flex items-center space-x-3 mb-6">
            <span class="text-sm text-slate-500 dark:text-[#6F767E] font-medium">UMKM Kuliner Sungailiat</span>
            <span class="text-xs font-bold px-2 py-0.5 rounded bg-[#F59E0B]/20 text-[#D97706] dark:text-[#F59E0B]">+100%</span>
          </div>

          <div class="flex items-end h-24 space-x-1">
            <div v-for="(h, i) in barHeights" :key="i"
              class="flex-1 rounded-t-sm transition-all duration-500"
              :class="i === activeBarIndex ? 'bg-slate-600 dark:bg-white' : 'bg-slate-200 dark:bg-[#2A2E33]'"
              :style="{ height: h + '%' }"
            ></div>
          </div>
          <div class="flex justify-between mt-2">
            <span v-for="label in barLabels" :key="label" class="text-xs text-slate-400 dark:text-[#6F767E]">{{ label }}</span>
          </div>
        </div>
      </div>

      <!-- Panel 2: Activity -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl flex flex-col pointer-events-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-bold flex items-center space-x-2 text-slate-800 dark:text-white">
            <Activity class="h-5 w-5 text-[#F59E0B]" />
            <span>Sebaran Data</span>
          </h3>
          <span class="text-xs text-slate-400 dark:text-[#6F767E] font-medium">{{ todayLabel }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 flex-1">
          <div class="bg-slate-50/60 dark:bg-[#111315]/60 border border-slate-100 dark:border-[#2A2E33] p-3.5 rounded-xl">
            <span class="text-xs text-slate-500 dark:text-[#6F767E] font-medium">Total Kategori</span>
            <div class="flex items-center space-x-2 mt-1">
              <span class="text-xl font-extrabold text-slate-800 dark:text-white">{{ loading ? '...' : stats?.total_categories }}</span>
              <div class="flex items-end h-6 space-x-[2px] flex-1">
                <div v-for="n in 5" :key="n" class="flex-1 bg-slate-200 dark:bg-[#2A2E33] rounded-t-sm" :style="{ height: (20 + n * 12) + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="bg-slate-50/60 dark:bg-[#111315]/60 border border-slate-100 dark:border-[#2A2E33] p-3.5 rounded-xl">
            <span class="text-xs text-slate-500 dark:text-[#6F767E] font-medium">Kelurahan</span>
            <div class="flex items-center space-x-2 mt-1">
              <span class="text-xl font-extrabold text-slate-800 dark:text-white">{{ loading ? '...' : stats?.total_villages }}</span>
              <svg class="h-5 flex-1 text-[#F59E0B]" preserveAspectRatio="none" viewBox="0 0 100 20" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M0,15 Q20,5 40,12 T80,8 T100,10" />
              </svg>
            </div>
          </div>

          <div class="bg-slate-50/60 dark:bg-[#111315]/60 border border-slate-100 dark:border-[#2A2E33] p-3.5 rounded-xl">
            <span class="text-xs text-slate-500 dark:text-[#6F767E] font-medium">Potensi Tinggi</span>
            <div class="flex items-center space-x-2 mt-1">
              <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ loading ? '...' : stats?.by_potential?.tinggi || 0 }}</span>
              <span class="text-xs text-slate-400 dark:text-[#6F767E]">UMKM</span>
            </div>
          </div>

          <div class="bg-slate-50/60 dark:bg-[#111315]/60 border border-slate-100 dark:border-[#2A2E33] p-3.5 rounded-xl">
            <span class="text-xs text-slate-500 dark:text-[#6F767E] font-medium">Potensi Rendah</span>
            <div class="flex items-center space-x-2 mt-1">
              <span class="text-xl font-extrabold text-rose-600 dark:text-rose-400">{{ loading ? '...' : stats?.by_potential?.rendah || 0 }}</span>
              <span class="text-xs text-slate-400 dark:text-[#6F767E]">UMKM</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel 3: Total Performance -->
      <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-2xl pointer-events-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-bold flex items-center space-x-2 text-slate-800 dark:text-white">
            <TrendingUp class="h-5 w-5 text-[#F59E0B]" />
            <span>Distribusi Potensi</span>
          </h3>
          <span class="text-xs text-slate-400 dark:text-[#6F767E] font-medium">{{ todayLabel }}</span>
        </div>

        <div class="flex items-center space-x-3 mb-4">
          <span class="text-xs text-slate-500 dark:text-[#6F767E]">100%</span>
          <div class="relative flex-1">
            <div class="absolute right-[14%] -top-5 bg-[#F59E0B] text-[#111315] text-xs font-bold px-2 py-0.5 rounded-md">
              {{ getPercentage(stats?.by_potential?.tinggi) }}%
            </div>
          </div>
        </div>

        <div class="relative h-24 mb-2">
          <Line
            v-if="monthlyRegistrations.length"
            :data="chartData"
            :options="chartOptions"
            :key="isDark ? 'dark' : 'light'"
          />
        </div>

        <div class="mt-5 space-y-3">
          <div>
            <div class="flex justify-between items-center mb-1">
              <span class="text-xs font-semibold text-slate-600 dark:text-[#F0F0F0] flex items-center space-x-1.5">
                <span class="h-2 w-2 bg-emerald-500 rounded-full inline-block"></span>
                <span>Tinggi</span>
              </span>
              <span class="text-xs font-bold text-slate-800 dark:text-white">{{ stats?.by_potential?.tinggi || 0 }}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-[#111315] h-1 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-1 rounded-full transition-all duration-700" :style="{ width: getPercentage(stats?.by_potential?.tinggi) + '%' }"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between items-center mb-1">
              <span class="text-xs font-semibold text-slate-600 dark:text-[#F0F0F0] flex items-center space-x-1.5">
                <span class="h-2 w-2 bg-amber-500 rounded-full inline-block"></span>
                <span>Sedang</span>
              </span>
              <span class="text-xs font-bold text-slate-800 dark:text-white">{{ stats?.by_potential?.sedang || 0 }}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-[#111315] h-1 rounded-full overflow-hidden">
              <div class="bg-amber-500 h-1 rounded-full transition-all duration-700" :style="{ width: getPercentage(stats?.by_potential?.sedang) + '%' }"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between items-center mb-1">
              <span class="text-xs font-semibold text-slate-600 dark:text-[#F0F0F0] flex items-center space-x-1.5">
                <span class="h-2 w-2 bg-rose-500 rounded-full inline-block"></span>
                <span>Rendah</span>
              </span>
              <span class="text-xs font-bold text-slate-800 dark:text-white">{{ stats?.by_potential?.rendah || 0 }}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-[#111315] h-1 rounded-full overflow-hidden">
              <div class="bg-rose-500 h-1 rounded-full transition-all duration-700" :style="{ width: getPercentage(stats?.by_potential?.rendah) + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Analysis Section -->
    <div v-if="!loadingAnalysis" class="space-y-6 pb-20">

      <!-- Key Metrics -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pointer-events-auto">
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Total UMKM</span>
            <div class="h-8 w-8 rounded-lg bg-[#F59E0B]/15 flex items-center justify-center">
              <Store class="h-4 w-4 text-[#F59E0B]" />
            </div>
          </div>
          <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ analysis.summary.total_umkm }}</span>
        </div>

        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Kategori</span>
            <div class="h-8 w-8 rounded-lg bg-indigo-500/15 flex items-center justify-center">
              <PieChart class="h-4 w-4 text-indigo-500" />
            </div>
          </div>
          <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ analysis.summary.total_categories }}</span>
        </div>

        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Kelurahan</span>
            <div class="h-8 w-8 rounded-lg bg-emerald-500/15 flex items-center justify-center">
              <MapPin class="h-4 w-4 text-emerald-500" />
            </div>
          </div>
          <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ analysis.summary.total_villages }}</span>
        </div>

        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-slate-400 dark:text-[#6F767E] uppercase tracking-wider">Rata-rata Skor</span>
            <div class="h-8 w-8 rounded-lg bg-rose-500/15 flex items-center justify-center">
              <TrendingUp class="h-4 w-4 text-rose-500" />
            </div>
          </div>
          <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ analysis.summary.avg_potential_score }}</span>
          <span class="text-sm text-slate-400 dark:text-[#6F767E] ml-1">/ 100</span>
        </div>
      </div>

      <!-- Row: Village Analysis + Score Distribution -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pointer-events-auto">

        <!-- Village Analysis -->
        <div class="lg:col-span-2 bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <MapPin class="h-5 w-5 text-[#F59E0B]" />
              <span>Analisis per Kelurahan</span>
            </h3>
            <span class="text-xs text-slate-400 dark:text-[#6F767E] font-medium">{{ analysis.summary.total_villages }} kelurahan</span>
          </div>

          <div class="space-y-4">
            <div v-for="(item, idx) in sortedVillages" :key="item.id" class="group">
              <div class="flex justify-between items-center mb-1.5">
                <div class="flex items-center space-x-2">
                  <span class="w-5 text-xs font-bold text-slate-400 dark:text-[#6F767E] text-right">{{ idx + 1 }}</span>
                  <span class="text-base font-semibold text-slate-700 dark:text-[#F0F0F0]">{{ item.name }}</span>
                  <span v-if="hasMaxUmkm(item.umkm_count)" class="text-xs font-bold px-1.5 py-0.5 rounded bg-[#F59E0B]/20 text-[#F59E0B]">TERBANYAK</span>
                </div>
                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ item.umkm_count }} <span class="text-xs font-normal text-slate-400 dark:text-[#6F767E]">UMKM</span></span>
              </div>
              <div class="flex items-center space-x-3">
                <div class="flex-1 bg-slate-100 dark:bg-[#111315] h-2.5 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-700"
                    :class="getVillageBarColor(item.umkm_count)"
                    :style="{ width: getVillagePercent(item.umkm_count) + '%' }"
                  ></div>
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-[#6F767E] w-10 text-right">{{ getVillagePercent(item.umkm_count) }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Score Distribution -->
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl flex flex-col">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <BarChart3 class="h-5 w-5 text-[#F59E0B]" />
              <span>Sebaran Skor Potensi</span>
            </h3>
            <span class="text-xs text-slate-400 dark:text-[#6F767E] font-medium">{{ analysis.summary.scored_umkm }} UMKM</span>
          </div>

          <div class="flex-1 flex flex-col justify-center space-y-5">
            <div v-for="bucket in analysis.score_distribution" :key="bucket.range" class="space-y-1.5">
              <div class="flex justify-between text-xs">
                <span class="font-medium text-slate-600 dark:text-[#F0F0F0]">{{ bucket.range }}</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ bucket.count }} <span class="font-normal text-slate-400 dark:text-[#6F767E]">UMKM</span></span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-[#111315] h-3 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-700"
                  :class="getScoreColor(bucket.range)"
                  :style="{ width: getScorePercent(bucket.count) + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <div class="mt-5 pt-4 border-t border-slate-100 dark:border-[#2A2E33]">
            <div class="flex items-center justify-between text-sm">
              <span class="font-medium text-slate-500 dark:text-[#6F767E]">Rata-rata Skor</span>
              <span class="text-xl font-extrabold text-slate-900 dark:text-white">{{ analysis.summary.avg_potential_score }}</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Row: Category Distribution + Category vs Potential -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pointer-events-auto">

        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <PieChart class="h-5 w-5 text-[#F59E0B]" />
              <span>Distribusi Kategori</span>
            </h3>
          </div>

          <div class="space-y-4">
            <div v-for="item in sortedCategories" :key="item.category" class="space-y-1">
              <div class="flex justify-between text-xs">
                <span class="font-medium text-slate-700 dark:text-[#F0F0F0] truncate max-w-[200px]">{{ item.category }}</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ item.count }}</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-[#111315] h-2 rounded-full overflow-hidden">
                <div class="bg-[#F59E0B] h-full rounded-full transition-all duration-700" :style="{ width: getCatPercent(item.count) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl lg:col-span-2">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <BarChart3 class="h-5 w-5 text-[#F59E0B]" />
              <span>Kategori vs Potensi</span>
            </h3>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
              <thead>
                <tr class="border-b border-slate-200 dark:border-[#2A2E33] text-slate-400 dark:text-[#6F767E] text-sm font-bold uppercase tracking-wider">
                  <th class="pb-3 pr-4">Kategori</th>
                  <th class="pb-3 pr-4 text-right">Total</th>
                  <th class="pb-3 pr-4 text-right">Rata-rata Skor</th>
                  <th class="pb-3 pr-4 text-right text-emerald-600">Tinggi</th>
                  <th class="pb-3 pr-4 text-right text-amber-600">Sedang</th>
                  <th class="pb-3 text-right text-rose-600">Rendah</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="item in categoryPotential"
                  :key="item.category"
                  class="border-b border-slate-100 dark:border-[#2A2E33] hover:bg-slate-50 dark:hover:bg-[#22262A] transition-colors"
                >
                  <td class="py-3 pr-4 font-medium text-slate-800 dark:text-white">{{ item.category }}</td>
                  <td class="py-3 pr-4 text-right font-bold text-slate-900 dark:text-white">{{ item.total }}</td>
                  <td class="py-3 pr-4 text-right">
                    <span class="font-bold" :class="getScoreTextClass(item.avg_score)">{{ item.avg_score }}</span>
                  </td>
                  <td class="py-3 pr-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">{{ item.tinggi }}</td>
                  <td class="py-3 pr-4 text-right font-semibold text-amber-600 dark:text-amber-400">{{ item.sedang }}</td>
                  <td class="py-3 text-right font-semibold text-rose-600 dark:text-rose-400">{{ item.rendah }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Top 5 UMKM -->
      <div class="grid grid-cols-1 gap-6 pointer-events-auto">
        <div class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2A2E33] pb-4 mb-5">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center space-x-2">
              <TrendingUp class="h-5 w-5 text-[#F59E0B]" />
              <span>Top 5 UMKM Potensi Tertinggi</span>
            </h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div
              v-for="(umkm, idx) in analysis.top_umkm"
              :key="umkm.id"
              class="relative bg-slate-50 dark:bg-[#111315] border border-slate-100 dark:border-[#2A2E33] rounded-xl p-4 hover:shadow-lg transition-shadow"
            >
              <div
                class="absolute -top-2 -left-2 h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold text-white"
                :class="idx === 0 ? 'bg-[#F59E0B]' : idx === 1 ? 'bg-slate-400' : idx === 2 ? 'bg-amber-700' : 'bg-slate-500'"
              >
                {{ idx + 1 }}
              </div>
              <div class="mt-1">
                <h4 class="font-bold text-base text-slate-800 dark:text-white truncate">{{ umkm.name }}</h4>
                <p class="text-sm text-slate-500 dark:text-[#6F767E] mt-0.5 truncate">{{ umkm.owner }}</p>
                <div class="mt-3 flex items-center justify-between">
                  <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-[#22262A] text-slate-600 dark:text-[#F0F0F0] truncate max-w-[100px]">{{ umkm.category }}</span>
                  <span class="text-sm font-extrabold" :class="getLevelColor(umkm.potential_level)">{{ umkm.potential_score }}</span>
                </div>
                <p class="text-xs text-slate-400 dark:text-[#6F767E] mt-1.5 truncate">{{ umkm.village_name }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Loading Skeleton for Analysis -->
    <div v-else class="space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pointer-events-auto">
        <div v-for="i in 4" :key="i" class="bg-white/85 dark:bg-[#1A1D1F]/90 backdrop-blur-xl border border-slate-200/40 dark:border-[#2A2E33]/60 p-5 rounded-2xl shadow-xl">
          <div class="h-4 w-20 bg-slate-100 dark:bg-[#22262A] rounded animate-pulse mb-4"></div>
          <div class="h-8 w-16 bg-slate-100 dark:bg-[#22262A] rounded animate-pulse"></div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { Store, TrendingUp, Activity, SlidersHorizontal, CalendarDays, MapPin, BarChart3, PieChart } from 'lucide-vue-next';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  LineElement,
  PointElement,
  LineController,
  CategoryScale,
  LinearScale,
  Filler,
  Tooltip,
} from 'chart.js';
import { useTheme } from '../../composables/useTheme';
import api from '../../services/api';

ChartJS.register(LineElement, PointElement, LineController, CategoryScale, LinearScale, Filler, Tooltip);

const { isDark } = useTheme();

interface PotentialStats {
  tinggi: number;
  sedang: number;
  rendah: number;
}

interface StatsData {
  total_umkm: number;
  total_categories: number;
  total_villages: number;
  by_potential: PotentialStats;
}

interface VillageItem {
  id: number;
  name: string;
  umkm_count: number;
  population: number | null;
  density: number | null;
}

interface CategoryItem {
  category: string;
  count: number;
}

interface BucketItem {
  range: string;
  min: number;
  max: number;
  count: number;
}

interface TopUmkm {
  id: number;
  name: string;
  owner: string;
  category: string;
  potential_score: number;
  potential_level: string;
  village_name: string;
}

interface CategoryPotential {
  category: string;
  total: number;
  avg_score: number;
  tinggi: number;
  sedang: number;
  rendah: number;
}

interface AnalysisData {
  summary: {
    total_umkm: number;
    total_categories: number;
    total_villages: number;
    avg_potential_score: number;
    scored_umkm: number;
  };
  village_analysis: VillageItem[];
  category_analysis: CategoryItem[];
  potential_distribution: { level: string; count: number }[];
  score_distribution: BucketItem[];
  top_umkm: TopUmkm[];
  category_potential: CategoryPotential[];
}

const stats = ref<StatsData | null>(null);
const loading = ref(true);

const analysis = ref<AnalysisData>({
  summary: { total_umkm: 0, total_categories: 0, total_villages: 0, avg_potential_score: 0, scored_umkm: 0 },
  village_analysis: [],
  category_analysis: [],
  potential_distribution: [],
  score_distribution: [],
  top_umkm: [],
  category_potential: [],
});
const loadingAnalysis = ref(true);

// Mini bar chart data
const barHeights = ref<number[]>([]);
const barLabels = ['Jan', 'Mar', 'Mei', 'Jul', 'Sep', 'Nov'];
const activeBarIndex = ref(0);

const monthlyRegistrations = ref<number[]>([]);

const chartData = computed(() => ({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
  datasets: [{
    data: monthlyRegistrations.value,
    borderColor: '#F59E0B',
    backgroundColor: (ctx: any) => {
      if (!ctx.chart.chartArea) return 'transparent';
      const { ctx: c, chartArea: { top, bottom } } = ctx.chart;
      const grad = c.createLinearGradient(0, top, 0, bottom);
      grad.addColorStop(0, 'rgba(245, 158, 11, 0.25)');
      grad.addColorStop(1, 'rgba(245, 158, 11, 0)');
      return grad;
    },
    fill: true,
    tension: 0.4,
    pointRadius: 0,
    pointHitRadius: 6,
    borderWidth: 2,
  }]
}));

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { tooltip: { enabled: false }, legend: { display: false } },
  scales: {
    x: {
      display: false,
      grid: { display: false },
    },
    y: {
      display: false,
      grid: { display: false },
      beginAtZero: true,
    },
  },
  elements: { point: { radius: 0 } },
}));

const todayLabel = computed(() => {
  const d = new Date();
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
});

// Computed
const sortedVillages = computed(() =>
  [...analysis.value.village_analysis].sort((a, b) => b.umkm_count - a.umkm_count)
);

const maxVillageCount = computed(() =>
  Math.max(...analysis.value.village_analysis.map(v => v.umkm_count), 1)
);

const sortedCategories = computed(() =>
  [...analysis.value.category_analysis].sort((a, b) => b.count - a.count)
);

const totalCatCount = computed(() =>
  analysis.value.category_analysis.reduce((s, c) => s + c.count, 0)
);

const categoryPotential = computed(() =>
  [...analysis.value.category_potential].sort((a, b) => b.total - a.total)
);

const totalPotentialCount = computed(() =>
  analysis.value.score_distribution.reduce((s, b) => s + b.count, 0)
);

function hasMaxUmkm(count: number): boolean {
  return count === maxVillageCount.value && count > 0;
}

function getVillagePercent(count: number): number {
  if (maxVillageCount.value === 0) return 0;
  return Math.round((count / maxVillageCount.value) * 100);
}

function getVillageBarColor(count: number): string {
  if (maxVillageCount.value === 0) return 'bg-slate-300';
  const ratio = count / maxVillageCount.value;
  if (ratio >= 0.8) return 'bg-emerald-500';
  if (ratio >= 0.5) return 'bg-[#F59E0B]';
  if (ratio >= 0.3) return 'bg-amber-400';
  return 'bg-slate-300 dark:bg-slate-600';
}

function getScorePercent(count: number): number {
  if (totalPotentialCount.value === 0) return 0;
  return Math.round((count / totalPotentialCount.value) * 100);
}

function getScoreColor(range: string): string {
  if (range.startsWith('0') || range.startsWith('21')) return 'bg-rose-500';
  if (range.startsWith('41')) return 'bg-amber-400';
  if (range.startsWith('61')) return 'bg-[#F59E0B]';
  return 'bg-emerald-500';
}

function getCatPercent(count: number): number {
  if (totalCatCount.value === 0) return 0;
  return Math.round((count / totalCatCount.value) * 100);
}

function getLevelColor(level: string | null): string {
  if (level === 'tinggi') return 'text-emerald-600 dark:text-emerald-400';
  if (level === 'sedang') return 'text-amber-600 dark:text-amber-400';
  return 'text-rose-600 dark:text-rose-400';
}

function getScoreTextClass(score: number): string {
  if (score >= 80) return 'text-emerald-600 dark:text-emerald-400';
  if (score >= 60) return 'text-amber-600 dark:text-amber-400';
  return 'text-rose-600 dark:text-rose-400';
}

function getPercentage(value: number | undefined): number {
  if (!value || !stats.value) return 0;
  const total = stats.value.total_umkm;
  if (total === 0) return 0;
  return Math.round((value / total) * 100);
}

async function fetchStats() {
  loading.value = true;
  try {
    const response = await api.get('/dashboard/stats');
    stats.value = response.data.data;

    const regRes = await api.get('/dashboard/registrations');
    const regData = regRes.data.data || [];

    if (regData.length > 0) {
      const monthlyCounts = new Array(12).fill(0);
      regData.forEach((item: any) => {
        const monthIdx = item.month - 1;
        monthlyCounts[monthIdx] = item.count;
      });
      monthlyRegistrations.value = monthlyCounts;
      const maxCount = Math.max(...monthlyCounts, 1);
      barHeights.value = monthlyCounts.map(count => count > 0 ? Math.max((count / maxCount) * 100, 10) : 5);
      activeBarIndex.value = new Date().getMonth();
    } else {
      barHeights.value = new Array(12).fill(5);
      activeBarIndex.value = new Date().getMonth();
    }
  } catch (err: any) {
    console.error('Error fetching dashboard stats:', err);
  } finally {
    loading.value = false;
  }
}

async function fetchAnalysis() {
  loadingAnalysis.value = true;
  try {
    const res = await api.get('/dashboard/analysis');
    analysis.value = res.data.data;
  } catch (err: any) {
    console.error('Error fetching analysis:', err);
  } finally {
    loadingAnalysis.value = false;
  }
}

onMounted(() => {
  fetchStats();
  fetchAnalysis();
});
</script>
