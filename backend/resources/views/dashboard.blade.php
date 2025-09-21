@extends('layout')

@section('content')
  <div class="card">
    <div class="grid cols-2">
      <div>
        <h3>Total Profit</h3>
        <div id="totalProfit" style="font-size:28px;font-weight:700">₹ 0.00</div>
      </div>
      <div>
        <h3>Efficiency Score</h3>
        <div id="efficiency" style="font-size:28px;font-weight:700">0%</div>
      </div>
    </div>
  </div>

  <div class="grid cols-2">
    <div class="card">
      <h4>On-time vs Late Deliveries</h4>
      <canvas id="onTimeChart" style="height:260px"></canvas>
    </div>

    <div class="card">
      <h4>Fuel Cost Breakdown</h4>
      <canvas id="fuelChart" style="height:260px"></canvas>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  async function loadDashboard() {
    try {
      // GET simulation history, pick latest
      const res = await axios.get('/api/simulations');
      const sims = res.data;
      if (!sims || sims.length === 0) {
        document.getElementById('totalProfit').textContent = '₹ 0.00';
        document.getElementById('efficiency').textContent = '0%';
        return;
      }
      const latest = sims[0].results;
      document.getElementById('totalProfit').textContent = '₹ ' + (latest.total_profit ?? 0).toFixed(2);
      document.getElementById('efficiency').textContent = (latest.efficiency_score ?? 0).toFixed(2) + '%';

      // On-time chart
      const onTimeCtx = document.getElementById('onTimeChart').getContext('2d');
      const onTimeData = {
        labels: ['On Time','Late'],
        datasets: [{ label:'Deliveries', data: [latest.on_time_deliveries || 0, latest.late_deliveries || 0], backgroundColor:['#10B981','#EF4444'] }]
      };
      new Chart(onTimeCtx, { type:'doughnut', data:onTimeData, options:{responsive:true, maintainAspectRatio:false} });

      // Fuel breakdown: aggregate by order or show totals
      const fuelTotals = latest.assignments?.reduce((acc,a)=>acc + (parseFloat(a.fuel_cost)||0),0) || 0;
      // We'll show fuel share vs profit (for visualization)
      const fuelCtx = document.getElementById('fuelChart').getContext('2d');
      const fuelData = {
        labels: ['Fuel Cost','Remaining Profit'],
        datasets:[{ label:'Costs', data: [fuelTotals, Math.max((latest.total_profit||0),0)], backgroundColor:['#F59E0B','#6366F1'] }]
      };
      new Chart(fuelCtx, { type:'pie', data:fuelData, options:{responsive:true, maintainAspectRatio:false} });

    } catch (err) {
      console.error(err);
      alert('Failed to load dashboard: ' + (err.response?.data?.message || err.message));
    }
  }

  document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endpush
