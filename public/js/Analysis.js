const USE_API = false;
const API_URL = '/api/analisis';

// total = nilai transaksi/penjualan (ribu Rp), modal = estimasi modal/HPP (ribu Rp)
const DATA_DUMMY = [
  { nama:'Bawang Merah', kategori:'Bumbu',   jumlah:12, total:360,  modal:250, bulanLalu:1 },
  { nama:'Bawang Putih', kategori:'Bumbu',   jumlah:8,  total:240,  modal:170, bulanLalu:2 },
  { nama:'Cabai Rawit',  kategori:'Bumbu',   jumlah:5,  total:300,  modal:210, bulanLalu:1 },
  { nama:'Kunyit',       kategori:'Bumbu',   jumlah:6,  total:90,   modal:60,  bulanLalu:4 },
  { nama:'Bayam',        kategori:'Sayuran', jumlah:15, total:150,  modal:100, bulanLalu:1 },
  { nama:'Kangkung',     kategori:'Sayuran', jumlah:20, total:160,  modal:110, bulanLalu:2 },
  { nama:'Wortel',       kategori:'Sayuran', jumlah:25, total:325,  modal:220, bulanLalu:3 },
  { nama:'Kol',          kategori:'Sayuran', jumlah:30, total:270,  modal:185, bulanLalu:5 },
  { nama:'Tomat',        kategori:'Sayuran', jumlah:18, total:216,  modal:150, bulanLalu:6 },
  { nama:'Ayam Potong',  kategori:'Daging',  jumlah:40, total:1400, modal:980, bulanLalu:1 },
  { nama:'Daging Sapi',  kategori:'Daging',  jumlah:22, total:2640, modal:1900,bulanLalu:2 },
  { nama:'Ikan Nila',    kategori:'Daging',  jumlah:28, total:980,  modal:700, bulanLalu:4 },
  { nama:'Telur',        kategori:'Daging',  jumlah:50, total:1500, modal:1120,bulanLalu:3 },
  { nama:'Udang',        kategori:'Daging',  jumlah:10, total:1000, modal:740, bulanLalu:7 },
  { nama:'Teh Celup',    kategori:'Minuman', jumlah:35, total:420,  modal:270, bulanLalu:2 },
  { nama:'Kopi Bubuk',   kategori:'Minuman', jumlah:12, total:840,  modal:560, bulanLalu:3 },
  { nama:'Gula Pasir',   kategori:'Minuman', jumlah:45, total:675,  modal:510, bulanLalu:1 },
  { nama:'Susu Cair',    kategori:'Minuman', jumlah:24, total:600,  modal:420, bulanLalu:8 },
  { nama:'Beras',        kategori:'Lainnya', jumlah:60, total:780,  modal:580, bulanLalu:1 },
  { nama:'Minyak Goreng',kategori:'Lainnya', jumlah:30, total:540,  modal:410, bulanLalu:2 },
  { nama:'Tepung Terigu',kategori:'Lainnya', jumlah:38, total:456,  modal:320, bulanLalu:5 },
  { nama:'Garam',        kategori:'Lainnya', jumlah:14, total:56,   modal:35,  bulanLalu:9 },
  { nama:'Santan',       kategori:'Lainnya', jumlah:16, total:320,  modal:225, bulanLalu:10 },
  { nama:'Kecap',        kategori:'Bumbu',   jumlah:9,  total:135,  modal:90,  bulanLalu:11 },
];

const WARNA = {
  Sayuran:'#3f9468', Daging:'#6b1a2b', Bumbu:'#e0a043', Minuman:'#5b8db8', Lainnya:'#a9a0a3'
};

let semuaData = [];
let chartKeuntungan, chartKategori;

const rupiah = (ribu) => 'Rp ' + Math.round(ribu * 1000).toLocaleString('id-ID');
const untungItem = (d) => (d.total || 0) - (d.modal ?? Math.round((d.total || 0) * 0.7));

/* Escape teks dari database sebelum dimasukkan lewat innerHTML */
const esc = (s) => String(s).replace(/[&<>"']/g, c => (
  { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]
));

function tampilPesan(teks) {
  const ul = document.getElementById('insights');
  if (ul) ul.innerHTML = `<li><span>ℹ️</span><span>${esc(teks)}</span></li>`;
}

async function loadData() {
  if (!USE_API) return DATA_DUMMY;

  try {
    const res = await fetch(API_URL, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const json = await res.json();
    return Array.isArray(json) ? json : (json.data || []);
  } catch (err) {
    console.error('Gagal memuat data analisis:', err);
    tampilPesan('Gagal memuat data. Coba muat ulang halaman.');
    return [];
  }
}

function filtered() {
  const periode  = +document.getElementById('filterPeriode').value;
  const kategori = document.getElementById('filterKategori').value;
  return semuaData.filter(d =>
    d.bulanLalu <= periode && (kategori === 'semua' || d.kategori === kategori)
  );
}

function render() {
  const data = filtered();

  /* KPI */
  const total = data.reduce((s,d)=>s+d.total,0);
  const totalUntung = data.reduce((s,d)=>s+untungItem(d),0);
  document.getElementById('kpiTrx').textContent   = data.length;
  document.getElementById('kpiTotal').textContent = rupiah(total);
  document.getElementById('kpiRata').textContent  = data.length ? rupiah(totalUntung/data.length) : '-';

  /* Grafik Keuntungan per Item/Transaksi (menggantikan scatter plot di #chartScatter) */
  const labelKeuntungan = data.map(d => d.nama);
  const nilaiKeuntungan = data.map(d => untungItem(d) * 1000);
  chartKeuntungan.data.labels = labelKeuntungan;
  chartKeuntungan.data.datasets[0].label = 'Keuntungan (Rp)';
  chartKeuntungan.data.datasets[0].data = nilaiKeuntungan;
  chartKeuntungan.data.datasets[0].pointBackgroundColor = nilaiKeuntungan.map(v => v < 0 ? '#dc2626' : '#16a34a');
  chartKeuntungan.update();

  /* Bar: total per kategori */
  const perKat = {};
  data.forEach(d => perKat[d.kategori] = (perKat[d.kategori]||0) + d.total);
  const labels = Object.keys(perKat);
  chartKategori.data.labels = labels;
  chartKategori.data.datasets[0].data = labels.map(k => perKat[k] * 1000);
  chartKategori.data.datasets[0].backgroundColor = labels.map(k => WARNA[k] || '#999');
  chartKategori.update();

  /* Ringkasan */
  const ul = document.getElementById('insights');
  if (!data.length) { tampilPesan('Tidak ada data untuk filter ini.'); return; }

  const katTop  = [...labels].sort((a,b)=>perKat[b]-perKat[a])[0];
  const untungTop = [...data].sort((a,b)=>untungItem(b)-untungItem(a))[0];
  ul.innerHTML = `
    <li><span>🧾</span><span><b>${data.length}</b> data dengan total transaksi <b>${rupiah(total)}</b> dan total keuntungan <b>${rupiah(totalUntung)}</b>.</span></li>
    <li><span>🏆</span><span>Kategori terbesar: <b>${esc(katTop)}</b> (${rupiah(perKat[katTop])}).</span></li>
    <li><span>📈</span><span>Keuntungan tertinggi: <b>${esc(untungTop.nama)}</b> sebesar <b>${rupiah(untungItem(untungTop))}</b>.</span></li>`;
}

async function init() {
  Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
  Chart.defaults.color = '#8a7f83';

  semuaData = await loadData();

  /* isi dropdown kategori */
  const sel = document.getElementById('filterKategori');
  [...new Set(semuaData.map(d=>d.kategori))].forEach(k => {
    const o = document.createElement('option'); o.value = k; o.textContent = k; sel.appendChild(o);
  });

  chartKeuntungan = new Chart(document.getElementById('chartScatter'), {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'Keuntungan (Rp)',
        data: [],
        borderColor: '#16a34a',
        backgroundColor: 'rgba(22, 163, 74, 0.14)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.3,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins: {
        legend: { position:'bottom' },
        tooltip: {
          callbacks: {
            label: c => {
              const item = filtered()[c.dataIndex];
              const nominal = Math.round(c.raw || 0);
              const extra = item ? ` (Trx: ${rupiah(item.total)}, Modal: ${rupiah(item.modal ?? item.total * 0.7)})` : '';
              return `Keuntungan: Rp ${nominal.toLocaleString('id-ID')}${extra}`;
            }
          }
        }
      },
      scales: {
        x: { grid:{color:'#f1ebe8'} },
        y: {
          grid:{color:'#f1ebe8'},
          ticks:{ callback: v => 'Rp ' + Math.round(v/1000).toLocaleString('id-ID') + 'rb' }
        }
      }
    }
  });

  chartKategori = new Chart(document.getElementById('chartKategori'), {
    type: 'bar',
    data: { labels: [], datasets: [{ data: [], borderRadius: 8 }] },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: c => rupiah(c.raw/1000) } } },
      scales: {
        y: { grid:{color:'#f1ebe8'}, ticks:{ callback: v => (v/1e6) + ' jt' } },
        x: { grid:{display:false} }
      }
    }
  });

  document.getElementById('filterPeriode').addEventListener('change', render);
  document.getElementById('filterKategori').addEventListener('change', render);
  render();
}

document.addEventListener('DOMContentLoaded', init);