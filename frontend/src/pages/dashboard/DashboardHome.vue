<template>
  <div class="dashboard-home">
    <header class="dh-header">
      <div>
        <h1>Welcome back, Admin</h1>
        <p class="subtitle">Overview of users, orders and revenue — updated live</p>
      </div>
      <div class="header-actions">
        <button class="btn primary dd-button">Create Report</button>
      </div>
    </header>

    <section class="stats-grid">
      <article class="stat-card" v-for="stat in stats" :key="stat.key">
        <div class="card-top">
          <div class="left">
            <div class="icon" v-html="stat.icon"></div>
            <div class="meta">
              <div class="value">{{ formatValue(stat.value, stat.type) }}</div>
              <div class="label">{{ stat.title }}</div>
            </div>
          </div>
          <div class="change" :class="stat.change >= 0 ? 'up' : 'down'">
            <svg v-if="stat.change >= 0" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12l7-7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 19V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 12l-7 7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="pct">{{ Math.abs(stat.change) }}%</span>
          </div>
        </div>

        <div class="sparkline" v-html="renderSparkline(stat.sparkData, stat.color)"></div>
      </article>
    </section>

    <section class="bottom-panels">
      <div class="panel recent-orders">
        <h3>Recent Orders</h3>
        <ul>
          <li v-for="order in recentOrders" :key="order.id">
            <div class="o-left">
              <strong>#{{ order.id }}</strong>
              <small class="muted">{{ order.customer }}</small>
            </div>
            <div class="o-right">
              <span class="amount">{{ order.amount }}</span>
              <span class="status" :data-status="order.status">{{ order.status }}</span>
            </div>
          </li>
        </ul>
      </div>

      <div class="panel quick-stats">
        <h3>Quick Insights</h3>
        <div class="insights">
          <div class="insight">
            <div class="num">12</div>
            <div class="lbl">Pending Tickets</div>
          </div>
          <div class="insight">
            <div class="num">98%</div>
            <div class="lbl">Uptime</div>
          </div>
          <div class="insight">
            <div class="num">4</div>
            <div class="lbl">Deploys Today</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref([
  {
    key: 'users',
    title: 'Total Users',
    value: 128,
    change: 6,
    type: 'number',
    color: '#7c3aed',
    icon: `<svg viewBox="0 0 24 24" width="28" height="28" fill="none"><path d="M12 12a4 4 0 100-8 4 4 0 000 8z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    sparkData: [5, 8, 6, 9, 12, 10, 11]
  },
  {
    key: 'orders',
    title: 'Total Orders',
    value: 76,
    change: -3,
    type: 'number',
    color: '#059669',
    icon: `<svg viewBox="0 0 24 24" width="28" height="28" fill="none"><path d="M3 3h18v4H3z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 21V7" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    sparkData: [4, 6, 5, 7, 6, 8, 7]
  },
  {
    key: 'revenue',
    title: 'Total Revenue',
    value: 5240,
    change: 11,
    type: 'currency',
    color: '#d97706',
    icon: `<svg viewBox="0 0 24 24" width="28" height="28" fill="none"><path d="M12 1v22" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 5H9.5a3.5 3.5 0 000 7H14a3.5 3.5 0 010 7H7" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    sparkData: [20, 30, 25, 40, 38, 50, 48]
  }
])

const recentOrders = ref([
  { id: 1029, customer: 'Jane Doe', amount: '$120.00', status: 'Paid' },
  { id: 1030, customer: 'John Smith', amount: '$54.00', status: 'Processing' },
  { id: 1031, customer: 'Acme Co', amount: '$999.00', status: 'Pending' }
])

function formatValue(v, type) {
  if (type === 'currency') {
    return `$${Number(v).toLocaleString()}`
  }
  if (v >= 1000) return `${(v / 1000).toFixed(1)}k`
  return `${v}`
}

function renderSparkline(values, color = '#4f46e5') {
  if (!values || !values.length) return ''
  const w = 160
  const h = 36
  const max = Math.max(...values)
  const min = Math.min(...values)
  const len = values.length
  const points = values.map((v, i) => {
    const x = (i / (len - 1)) * w
    const y = h - ((v - min) / (max - min || 1)) * h
    return `${x},${y}`
  }).join(' ')

  // simple polyline sparkline
  return `<svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="g" x1="0" x2="0" y1="0" y2="1">
        <stop offset="0%" stop-color="${color}" stop-opacity="0.18" />
        <stop offset="100%" stop-color="${color}" stop-opacity="0" />
      </linearGradient>
    </defs>
    <polyline fill="none" stroke="${color}" stroke-width="2" points="${points}" stroke-linejoin="round" stroke-linecap="round" />
    <polyline points="${points} ${w},${h} 0,${h}" fill="url(#g)" stroke="none" />
  </svg>`
}

onMounted(() => {
  // simulate refresh from API after load
  setTimeout(() => {
    stats.value[0].value = 134
    stats.value[2].value = 5480
  }, 900)
})
</script>

<style scoped>
.dashboard-home{padding:22px;font-family:Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial}
.dh-header{display:flex;justify-content:space-between;align-items:center}
.dh-header h1{margin:0;font-size:20px;color:#0f172a}
.subtitle{color:#64748b;margin-top:6px;font-size:13px}
.header-actions .btn{padding:8px 12px;border-radius:8px;border:none;cursor:pointer}
.btn.primary{background:#4CAF50;color:#fff;box-shadow:0 6px 18px rgba(76,175,80,0.12)}

.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-top:18px}
.stat-card{background:linear-gradient(180deg, #ffffff 0%, #fbfbff 100%);border-radius:12px;padding:14px 16px;box-shadow:0 6px 18px rgba(15,23,42,0.06);border:1px solid rgba(15,23,42,0.03);display:flex;flex-direction:column;justify-content:space-between}
.card-top{display:flex;justify-content:space-between;align-items:center}
.left{display:flex;align-items:center;gap:12px}
.icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#6366f1 0%, #7c3aed 100%);box-shadow:0 6px 12px rgba(124,58,237,0.08);color:#fff}
.meta .value{font-size:20px;font-weight:700;color:#0f172a}
.meta .label{color:#64748b;font-size:12px;margin-top:4px}
.change{display:flex;align-items:center;gap:6px;padding:6px 8px;border-radius:8px;font-weight:600;color:#065f46}
.change svg{color:inherit}
.change.up{background:rgba(16,185,129,0.08);color:#059669}
.change.down{background:rgba(239,68,68,0.06);color:#ef4444}
.pct{font-size:13px}

.sparkline{margin-top:12px}

.bottom-panels{display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-top:18px}
.panel{background:#fff;border-radius:12px;padding:14px;box-shadow:0 6px 18px rgba(2,6,23,0.04)}
.panel h3{margin:0 0 10px 0;font-size:15px}
.recent-orders ul{list-style:none;padding:0;margin:0}
.recent-orders li{display:flex;justify-content:space-between;padding:10px 0;border-top:1px dashed #f1f5f9}
.recent-orders li:first-child{border-top:0}
.o-left .muted{display:block;color:#64748b;font-size:12px}
.o-right{display:flex;flex-direction:column;align-items:flex-end}
.amount{font-weight:700}
.status[data-status="Paid"]{color:#059669}
.status[data-status="Processing"]{color:#f59e0b}
.status[data-status="Pending"]{color:#ef4444}

.quick-stats .insights{display:flex;gap:12px}
.insight{flex:1;background:linear-gradient(90deg,#f8fafc,#fff);border-radius:8px;padding:12px;text-align:center}
.insight .num{font-size:18px;font-weight:700}
.insight .lbl{color:#64748b;font-size:12px;margin-top:6px}

@media (max-width:900px){.bottom-panels{grid-template-columns:1fr}.dh-header{flex-direction:column;align-items:flex-start;gap:10px}.header-actions{align-self:stretch}}
</style>
  