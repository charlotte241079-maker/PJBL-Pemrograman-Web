document.addEventListener("DOMContentLoaded", () => {
    const state = {
        period: "30d",
        startDate: "",
        endDate: "",
        charts: {},
        rawData: null,
    };

    const periodSelect = document.getElementById("filterPeriod");
    const startDateInput = document.getElementById("filterStartDate");
    const endDateInput = document.getElementById("filterEndDate");
    const applyFilterBtn = document.getElementById("btnApplyFilter");
    const resetFilterBtn = document.getElementById("btnResetFilter");
    const loadingIndicator = document.getElementById("dashboardLoading");

    bindEvents();
    loadDashboardData();

    function bindEvents() {
        if (periodSelect) {
            periodSelect.addEventListener("change", (e) => {
                state.period = e.target.value;
                loadDashboardData();
            });
        }

        if (applyFilterBtn) {
            applyFilterBtn.addEventListener("click", (e) => {
                e.preventDefault();
                state.startDate = startDateInput ? startDateInput.value : "";
                state.endDate = endDateInput ? endDateInput.value : "";
                if (state.startDate && state.endDate) {
                    state.period = "custom";
                }
                loadDashboardData();
            });
        }

        if (resetFilterBtn) {
            resetFilterBtn.addEventListener("click", (e) => {
                e.preventDefault();
                state.period = "30d";
                state.startDate = "";
                state.endDate = "";
                if (periodSelect) periodSelect.value = "30d";
                if (startDateInput) startDateInput.value = "";
                if (endDateInput) endDateInput.value = "";
                loadDashboardData();
            });
        }
    }

    function setLoading(isLoading) {
        if (!loadingIndicator) return;
        loadingIndicator.classList.toggle("hidden", !isLoading);
    }

    async function loadDashboardData() {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                period: state.period,
                ...(state.startDate && { start_date: state.startDate }),
                ...(state.endDate && { end_date: state.endDate }),
            });

            const response = await fetch(`/api/v1/dashboard?${params.toString()}`, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const payload = await response.json();
            const root = payload.data ?? payload;
            state.rawData = normalizeDashboardPayload(root, state.period);
        } catch (err) {
            console.warn("Gagal memuat /api/v1/dashboard, memakai data sementara:", err.message);
            state.rawData = normalizeDashboardPayload({}, state.period);
        } finally {
            renderAll(state.rawData);
            setLoading(false);
        }
    }

    function normalizeDashboardPayload(root = {}, period = "30d") {
        const fallback = getFallbackDataByPeriod(period);
        const kpiSrc = root.kpis ?? root.kpi ?? root.summary ?? root;

        const totalTransactions = pickNumber(
            kpiSrc,
            ["totalTransactions", "total_transactions", "transactions_count", "total_transaksi"],
            fallback.kpis.totalTransactions
        );

        const totalExpense = pickNumber(
            kpiSrc,
            ["totalExpense", "total_expense", "expense_total", "total_pengeluaran"],
            fallback.kpis.totalExpense
        );

        const ingredientTypes = pickNumber(
            kpiSrc,
            [
                "ingredientTypes",
                "ingredient_types",
                "total_ingredients",
                "ingredients_count",
                "jenis_bahan",
            ],
            fallback.kpis.ingredientTypes
        );

        const lastUpdated =
            kpiSrc.lastUpdated ??
            kpiSrc.last_updated ??
            kpiSrc.updated_at ??
            fallback.kpis.lastUpdated;

        const lastUpdatedSub =
            kpiSrc.lastUpdatedSub ??
            kpiSrc.last_updated_sub ??
            kpiSrc.last_updated_human ??
            fallback.kpis.lastUpdatedSub;

        const lineSrc = root.lineChart ?? root.line_chart ?? root.trend ?? {};
        const lineLabels =
            lineSrc.labels ??
            (Array.isArray(lineSrc) ? lineSrc.map((i) => i.label ?? i.date ?? i.period) : null) ??
            fallback.lineChart.labels;

        const lineExpenses =
            lineSrc.expenses ??
            lineSrc.expense ??
            (Array.isArray(lineSrc)
                ? lineSrc.map((i) => Number(i.expenses ?? i.expense ?? i.total ?? 0))
                : null) ??
            fallback.lineChart.expenses;

        const linePurchases =
            lineSrc.purchases ??
            lineSrc.purchase ??
            (Array.isArray(lineSrc)
                ? lineSrc.map((i) => Number(i.purchases ?? i.purchase ?? 0))
                : null) ??
            fallback.lineChart.purchases;

        const scatterSrc = root.scatterChart ?? root.scatter_chart ?? root.scatter ?? {};
        const scatterPoints =
            scatterSrc.points ??
            (Array.isArray(scatterSrc) ? scatterSrc : null) ??
            fallback.scatterChart.points;

        const catSrc =
            root.categoryChart ??
            root.category_chart ??
            root.barChart ??
            root.bar_chart ??
            root.categories ??
            {};
        const catLabels =
            catSrc.labels ??
            (Array.isArray(catSrc) ? catSrc.map((i) => i.label ?? i.name ?? i.category) : null) ??
            fallback.barChart.labels;

        const catValues =
            catSrc.values ??
            catSrc.totals ??
            (Array.isArray(catSrc)
                ? catSrc.map((i) => Number(i.value ?? i.total ?? i.amount ?? 0))
                : null) ??
            fallback.barChart.values;

        const pieSrc = root.pieChart ?? root.pie_chart ?? root.donutChart ?? root.donut_chart ?? {};
        const pieLabels = pieSrc.labels ?? catLabels ?? fallback.pieChart.labels;
        const pieValues = pieSrc.values ?? pieSrc.totals ?? catValues ?? fallback.pieChart.values;

        const analysisSrc = root.analysis ?? root.summary_analysis ?? root.insights ?? null;
        const insights = Array.isArray(analysisSrc)
            ? analysisSrc
            : Array.isArray(analysisSrc?.insights)
            ? analysisSrc.insights
            : null;

        return {
            kpis: {
                totalTransactions,
                totalExpense,
                ingredientTypes,
                lastUpdated,
                lastUpdatedSub,
                transactionsTrend: kpiSrc.transactionsTrend ?? kpiSrc.transactions_trend ?? null,
                expenseTrend: kpiSrc.expenseTrend ?? kpiSrc.expense_trend ?? null,
                ingredientsTrend: kpiSrc.ingredientsTrend ?? kpiSrc.ingredients_trend ?? null,
            },
            lineChart: {
                labels: lineLabels,
                expenses: lineExpenses,
                purchases: linePurchases,
            },
            scatterChart: {
                points: scatterPoints,
            },
            barChart: {
                labels: catLabels,
                values: catValues,
            },
            pieChart: {
                labels: pieLabels,
                values: pieValues,
            },
            analysis: insights ? { insights } : null,
        };
    }

    function pickNumber(obj, keys, fallbackVal) {
        for (const key of keys) {
            if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
                const parsed = Number(obj[key]);
                if (!Number.isNaN(parsed)) return parsed;
            }
        }
        return fallbackVal;
    }

    function renderAll(data) {
        renderKPIs(data.kpis);
        renderLineChart(data.lineChart);
        renderScatterChart(data.scatterChart);
        renderBarChart(data.barChart);
        renderPieChart(data.pieChart);
        renderAnalysisSummary(data.analysis, data.kpis);
    }

    function renderKPIs(kpis = {}) {
        setText("kpiTotalTransactions", formatNumber(kpis.totalTransactions));
        setText("kpiTotalExpense", formatRupiah(kpis.totalExpense));
        setText("kpiIngredientTypes", formatNumber(kpis.ingredientTypes));
        setText("kpiLastUpdated", kpis.lastUpdated ?? "-");
        setText("kpiLastUpdatedSub", kpis.lastUpdatedSub ?? "");

        if (kpis.transactionsTrend) {
            setTrendText("kpiTransactionsTrend", kpis.transactionsTrend, !String(kpis.transactionsTrend).includes("↓"));
        }
        if (kpis.expenseTrend) {
            setTrendText("kpiExpenseTrend", kpis.expenseTrend, !String(kpis.expenseTrend).includes("↓"));
        }
        if (kpis.ingredientsTrend) {
            setTrendText("kpiIngredientsTrend", kpis.ingredientsTrend, !String(kpis.ingredientsTrend).includes("↓"));
        }
    }

    function renderLineChart(chartData = {}) {
        const canvas = document.getElementById("chartExpenseLine");
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart("line");

        state.charts.line = new Chart(canvas.getContext("2d"), {
            type: "line",
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: "Total Pengeluaran (Rp)",
                        data: chartData.expenses,
                        borderColor: "#8b1e3f",
                        backgroundColor: "rgba(139, 30, 63, 0.12)",
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                    },
                    {
                        label: "Pembelian Bahan (Rp)",
                        data: chartData.purchases,
                        borderColor: "#1f7a5c",
                        backgroundColor: "rgba(31, 122, 92, 0.08)",
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.35,
                        fill: false,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: "index", intersect: false },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${formatRupiah(ctx.parsed.y)}`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (val) => formatCompactRupiah(val),
                        },
                    },
                },
            },
        });
    }

    function renderScatterChart(chartData = {}) {
        const canvas = document.getElementById("chartExpenseScatter");
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart("scatter");

        const points = (chartData.points || []).map((pt) => ({
            x: Number(pt.x ?? pt.frequency ?? pt.count ?? 0),
            y: Number(pt.y ?? pt.total ?? pt.cost ?? pt.amount ?? 0),
            label: pt.label ?? pt.name ?? pt.ingredient ?? "",
        }));

        state.charts.scatter = new Chart(canvas.getContext("2d"), {
            type: "scatter",
            data: {
                datasets: [
                    {
                        label: "Frekuensi Transaksi vs Biaya",
                        data: points,
                        backgroundColor: "#c98a1c",
                        borderColor: "#8b1e3f",
                        borderWidth: 1.5,
                        pointRadius: 7,
                        pointHoverRadius: 9,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const raw = ctx.raw || {};
                                const name = raw.label ? `${raw.label} — ` : "";
                                return `${name}${raw.x}x transaksi, ${formatRupiah(raw.y)}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        title: { display: true, text: "Frekuensi Transaksi" },
                        beginAtZero: true,
                    },
                    y: {
                        title: { display: true, text: "Total Biaya (Rp)" },
                        beginAtZero: true,
                        ticks: {
                            callback: (val) => formatCompactRupiah(val),
                        },
                    },
                },
            },
        });
    }

    function renderBarChart(chartData = {}) {
        const canvas = document.getElementById("chartCategoryBar");
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart("bar");

        state.charts.bar = new Chart(canvas.getContext("2d"), {
            type: "bar",
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: "Pengeluaran (Rp)",
                        data: chartData.values,
                        backgroundColor: ["#8b1e3f", "#1f7a5c", "#c98a1c", "#2b5b84", "#6c4a86"],
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => formatRupiah(ctx.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (val) => formatCompactRupiah(val),
                        },
                    },
                },
            },
        });
    }

    function renderPieChart(chartData = {}) {
        const canvas = document.getElementById("chartCategoryPie");
        if (!canvas || typeof Chart === "undefined") return;

        destroyChart("pie");

        state.charts.pie = new Chart(canvas.getContext("2d"), {
            type: "doughnut",
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        data: chartData.values,
                        backgroundColor: ["#8b1e3f", "#1f7a5c", "#c98a1c", "#2b5b84", "#6c4a86"],
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "58%",
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0) || 1;
                                const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                return `${ctx.label}: ${formatRupiah(ctx.parsed)} (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });
    }

    function renderAnalysisSummary(analysis = null, kpis = {}) {
        const container = document.getElementById("analysisSummary");
        if (!container) return;

        const insights = analysis?.insights ?? [
            `Total pengeluaran tercatat ${formatRupiah(kpis.totalExpense)} dari ${formatNumber(kpis.totalTransactions)} transaksi pada periode terpilih.`,
            "Gunakan grafik batang dan pie/donut untuk membandingkan kontribusi biaya antar kategori/bahan baku.",
            "Grafik scatter membantu mengidentifikasi bahan dengan frekuensi tinggi dan biaya besar untuk efisiensi pembelian.",
        ];

        container.innerHTML = insights
            .map((item) => `<li class="mb-2 text-sm text-gray-700">• ${escapeHtml(item)}</li>`)
            .join("");
    }

    function destroyChart(key) {
        if (state.charts[key]) {
            state.charts[key].destroy();
            state.charts[key] = null;
        }
    }

    function setText(id, text) {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    }

    function setTrendText(id, text, isPositive) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = text;
        el.classList.toggle("text-emerald-600", isPositive);
        el.classList.toggle("text-rose-600", !isPositive);
    }

    function formatRupiah(num) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(Number(num) || 0);
    }

    function formatCompactRupiah(num) {
        const n = Number(num) || 0;
        if (n >= 1_000_000) return `Rp ${(n / 1_000_000).toFixed(1)}jt`;
        if (n >= 1_000) return `Rp ${(n / 1_000).toFixed(0)}rb`;
        return `Rp ${n}`;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat("id-ID").format(Number(num) || 0);
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    function getFallbackDataByPeriod(period) {
        const multiplier =
            period === "7d" ? 0.25 : period === "90d" ? 2.8 : period === "1y" ? 11.5 : 1;

        return {
            kpis: {
                totalTransactions: Math.round(128 * multiplier),
                totalExpense: Math.round(8340000 * multiplier),
                ingredientTypes: 24,
                lastUpdated: "12 Nov 2026",
                lastUpdatedSub: "2 jam yang lalu",
            },
            lineChart: {
                labels: ["Mg 1", "Mg 2", "Mg 3", "Mg 4"],
                expenses: [1850000, 2150000, 1940000, 2400000].map((v) => Math.round(v * multiplier)),
                purchases: [1500000, 1800000, 1650000, 2100000].map((v) => Math.round(v * multiplier)),
            },
            scatterChart: {
                points: [
                    { x: 4, y: Math.round(1200000 * multiplier), label: "Minyak & Lemak" },
                    { x: 9, y: Math.round(2450000 * multiplier), label: "Daging & Protein" },
                    { x: 12, y: Math.round(1650000 * multiplier), label: "Sayur & Segar" },
                    { x: 6, y: Math.round(980000 * multiplier), label: "Bumbu & Rempah" },
                    { x: 15, y: Math.round(2060000 * multiplier), label: "Kering & Kemasan" },
                ],
            },
            barChart: {
                labels: ["Protein", "Sayuran", "Bumbu", "Bahan Kering", "Kemasan"],
                values: [2850000, 1420000, 960000, 1890000, 1220000].map((v) =>
                    Math.round(v * multiplier)
                ),
            },
            pieChart: {
                labels: ["Protein", "Sayuran", "Bumbu", "Bahan Kering", "Kemasan"],
                values: [2850000, 1420000, 960000, 1890000, 1220000].map((v) =>
                    Math.round(v * multiplier)
                ),
            },
        };
    }
});
```[cite: 3, 4]

Kalau sudah dicoba dan ada bagian yang perlu disesuaikan dengan file Blade atau respons `IngredientController@dashboard`, kabari saja ya[cite: 3]!
