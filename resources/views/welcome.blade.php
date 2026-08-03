<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEETA | Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }

        body {
            background: #F1F5F9;
            color: #1E293B;
        }

        /* ===== LAYOUT ===== */
        .wrapper { display: flex; min-height: 100vh; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 250px;
            background: #0F172A;
            color: #E2E8F0;
            padding: 24px 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar .logo {
            font-size: 22px;
            font-weight: 700;
            color: #F97316;
            padding: 0 24px 24px;
            border-bottom: 1px solid #1E293B;
        }

        .sidebar nav {
            margin-top: 20px;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #1E293B;
            color: #F97316;
            border-left: 3px solid #F97316;
        }

        /* ===== MAIN CONTENT ===== */
        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 24px 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .topbar h1 {
            font-size: 22px;
            font-weight: 600;
        }

        .topbar .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .admin-info .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #F97316;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* ===== STAT CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border-left: 4px solid #F97316;
        }

        .stat-card .label {
            font-size: 13px;
            color: #64748B;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #0F172A;
        }

        .stat-card .change {
            font-size: 12px;
            margin-top: 6px;
        }

        .change.positive { color: #16A34A; }
        .change.negative { color: #DC2626; }

        /* ===== TABLES SECTION ===== */
        .tables-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .card h2 {
            font-size: 16px;
            margin-bottom: 16px;
            color: #0F172A;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            text-align: left;
            color: #64748B;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 1px solid #E2E8F0;
        }

        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #F1F5F9;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge.pending { background: #FEF3C7; color: #92400E; }
        .badge.completed { background: #DCFCE7; color: #166534; }
        .badge.processing { background: #DBEAFE; color: #1E40AF; }
        .badge.cancelled { background: #FEE2E2; color: #991B1B; }

        .vendor-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #F1F5F9;
        }

        .vendor-row:last-child { border-bottom: none; }

        .vendor-info { display: flex; align-items: center; gap: 10px; }

        .vendor-info .v-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #0F172A;
            color: #F97316;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        .vendor-info .v-name { font-size: 13px; font-weight: 600; }
        .vendor-info .v-sub { font-size: 11px; color: #64748B; }

        .vendor-sales { font-size: 13px; font-weight: 700; color: #0F172A; }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .tables-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="wrapper">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <div class="logo">MEETA</div>
            <nav>
                <a href="#" class="active">📊 Dashboard</a>
                <a href="#">🏬 Vendors</a>
                <a href="#">📦 Orders</a>
                <a href="#">🛒 Products</a>
                <a href="#">💰 Payouts</a>
                <a href="#">👥 Customers</a>
                <a href="#">⚙️ Settings</a>
            </nav>
        </aside>

        <!-- ===== MAIN ===== -->
        <main class="main">

            <div class="topbar">
                <h1>Admin Dashboard</h1>
                <div class="admin-info">
                    <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                    <div class="avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                </div>
            </div>

            <!-- ===== STATS ===== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="label">Total Vendors</div>
                    <div class="value">{{ $totalVendors ?? 0 }}</div>
                    <div class="change positive">+{{ $newVendorsThisMonth ?? 0 }} this month</div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Orders</div>
                    <div class="value">{{ $totalOrders ?? 0 }}</div>
                    <div class="change positive">+{{ $ordersToday ?? 0 }} today</div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Revenue</div>
                    <div class="value">₹{{ number_format($totalRevenue ?? 0) }}</div>
                    <div class="change positive">+{{ $revenueGrowth ?? 0 }}% vs last month</div>
                </div>
                <div class="stat-card">
                    <div class="label">Pending Payouts</div>
                    <div class="value">₹{{ number_format($pendingPayouts ?? 0) }}</div>
                    <div class="change negative">{{ $pendingPayoutVendors ?? 0 }} vendors waiting</div>
                </div>
            </div>

            <!-- ===== TABLES ===== -->
            <div class="tables-grid">

                <!-- Recent Orders -->
                <div class="card">
                    <h2>Recent Orders</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->vendor_name }}</td>
                                <td>₹{{ number_format($order->total) }}</td>
                                <td>
                                    <span class="badge {{ strtolower($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#94A3B8; padding:20px;">
                                    No recent orders
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Top Vendors -->
                <div class="card">
                    <h2>Top Performing Vendors</h2>
                    @forelse($topVendors ?? [] as $vendor)
                    <div class="vendor-row">
                        <div class="vendor-info">
                            <div class="v-avatar">{{ substr($vendor->name, 0, 1) }}</div>
                            <div>
                                <div class="v-name">{{ $vendor->name }}</div>
                                <div class="v-sub">{{ $vendor->orders_count }} orders</div>
                            </div>
                        </div>
                        <div class="vendor-sales">₹{{ number_format($vendor->total_sales) }}</div>
                    </div>
                    @empty
                    <p style="color:#94A3B8; font-size:13px;">No vendor data available</p>
                    @endforelse
                </div>

            </div>

        </main>
    </div>

</body>
</html>