const USE_API = false;
const API_URL = '/api/analisis';

const DATA_DUMMY = [
  { nama:'Bawang Merah', kategori:'Bumbu',   jumlah:12, total:360, bulanLalu:1 },
  { nama:'Bawang Putih', kategori:'Bumbu',   jumlah:8,  total:240, bulanLalu:2 },
  { nama:'Cabai Rawit',  kategori:'Bumbu',   jumlah:5,  total:300, bulanLalu:1 },
  { nama:'Kunyit',       kategori:'Bumbu',   jumlah:6,  total:90,  bulanLalu:4 },
  { nama:'Bayam',        kategori:'Sayuran', jumlah:15, total:150, bulanLalu:1 },
  { nama:'Kangkung',     kategori:'Sayuran', jumlah:20, total:160, bulanLalu:2 },
  { nama:'Wortel',       kategori:'Sayuran', jumlah:25, total:325, bulanLalu:3 },
  { nama:'Kol',          kategori:'Sayuran', jumlah:30, total:270, bulanLalu:5 },
  { nama:'Tomat',        kategori:'Sayuran', jumlah:18, total:216, bulanLalu:6 },
  { nama:'Ayam Potong',  kategori:'Daging',  jumlah:40, total:1400,bulanLalu:1 },
  { nama:'Daging Sapi',  kategori:'Daging',  jumlah:22, total:2640,bulanLalu:2 },
  { nama:'Ikan Nila',    kategori:'Daging',  jumlah:28, total:980, bulanLalu:4 },
  { nama:'Telur',        kategori:'Daging',  jumlah:50, total:1500,bulanLalu:3 },
  { nama:'Udang',        kategori:'Daging',  jumlah:10, total:1000,bulanLalu:7 },
  { nama:'Teh Celup',    kategori:'Minuman', jumlah:35, total:420, bulanLalu:2 },
  { nama:'Kopi Bubuk',   kategori:'Minuman', jumlah:12, total:840, bulanLalu:3 },
  { nama:'Gula Pasir',   kategori:'Minuman', jumlah:45, total:675, bulanLalu:1 },
  { nama:'Susu Cair',    kategori:'Minuman', jumlah:24, total:600, bulanLalu:8 },
  { nama:'Beras',        kategori:'Lainnya', jumlah:60, total:780, bulanLalu:1 },
  { nama:'Minyak Goreng',kategori:'Lainnya', jumlah:30, total:540, bulanLalu:2 },
  { nama:'Tepung Terigu',kategori:'Lainnya', jumlah:38, total:456, bulanLalu:5 },
  { nama:'Garam',        kategori:'Lainnya', jumlah:14, total:56,  bulanLalu:9 },
  { nama:'Santan',       kategori:'Lainnya', jumlah:16, total:320, bulanLalu:10 },
  { nama:'Kecap',        kategori:'Bumbu',   jumlah:9,  total:135, bulanLalu:11 },
];

const WARNA = {
  Sayuran:'#3f9468', Daging:'#6b1a2b', Bumbu:'#e0a043', Minuman:'#5b8db8', Lainnya:'#a9a0a3'
};

let semuaData = [];
let chartScatter, chartKategori;

const rupiah = (ribu) => 'Rp ' + Math.round(ribu * 1000).toLocaleString('id-ID');

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

/* Korelasi Pearson antara jumlah beli dan total harga */
function korelasi(data) {
  const n = data.length;
  if (n < 3) return null;
  const x = data.map(d => d.jumlah), y = data.map(d => d.total);
  const mx = x.reduce((a,b)=>a+b,0)/n, my = y.reduce((a,b)=>a+b,0)/n;
  let sxy=0, sxx=0, syy=0;
  for (let i=0;i<n;i++){ sxy+=(x[i]-mx)*(y[i]-my); sxx+=(x[i]-mx)**2; syy+=(y[i]-my)**2; }
  return sxx && syy ? sxy / Math.sqrt(sxx*syy) : null;
}

function render() {
  const data = filtered();

  /* KPI */
  const total = data.reduce((s,d)=>s+d.total,0);
  document.getElementById('kpiTrx').textContent   = data.length;
  document.getElementById('kpiTotal').textContent = rupiah(total);
  document.getElementById('kpiRata').textContent  = data.length ? rupiah(total/data.length) : '-';

  /* Scatter: satu dataset per kategori */
  const kategori = [...new Set(data.map(d=>d.kategori))];
  chartScatter.data.datasets = kategori.map(k => ({
    label: k,
    data: data.filter(d=>d.kategori===k).map(d => ({ x:d.jumlah, y:d.total, nama:d.nama })),
    backgroundColor: WARNA[k] || '#999',
    pointRadius: 7, pointHoverRadius: 9,
  }));
  chartScatter.update();

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

  // pakai salinan [...labels] supaya urutan label grafik tidak berubah
  const katTop  = [...labels].sort((a,b)=>perKat[b]-perKat[a])[0];
  const itemTop = [...data].sort((a,b)=>b.total-a.total)[0];
  const r = korelasi(data);
  let teksR = 'Data terlalu sedikit untuk menghitung hubungan jumlah dan harga.';
  if (r !== null) {
    const kuat = Math.abs(r) >= 0.7 ? 'kuat' : Math.abs(r) >= 0.4 ? 'sedang' : 'lemah';
    teksR = `Hubungan antara jumlah beli dan total harga <b>${kuat}</b> (korelasi ${r.toFixed(2)}).`;
  }
  ul.innerHTML = `
    <li><span>🧾</span><span><b>${data.length}</b> pembelian dengan total <b>${rupiah(total)}</b>.</span></li>
    <li><span>🏆</span><span>Kategori pengeluaran terbesar: <b>${esc(katTop)}</b> (${rupiah(perKat[katTop])}).</span></li>
    <li><span>🔝</span><span>Pembelian tertinggi: <b>${esc(itemTop.nama)}</b> sebesar <b>${rupiah(itemTop.total)}</b>.</span></li>
    <li><span>📊</span><span>${teksR}</span></li>`;
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

  chartScatter = new Chart(document.getElementById('chartScatter'), {
    type: 'scatter',
    data: { datasets: [] },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins: {
        legend: { position:'bottom' },
        tooltip: { callbacks: { label: c => `${c.raw.nama}: ${c.raw.x} unit, ${rupiah(c.raw.y)}` } }
      },
      scales: {
        x: { title:{display:true,text:'Jumlah dibeli'}, grid:{color:'#f1ebe8'} },
        y: { title:{display:true,text:'Total harga (ribu Rp)'}, grid:{color:'#f1ebe8'} }
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
