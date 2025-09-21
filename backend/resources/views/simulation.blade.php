@extends('layout')

@section('content')
  <div class="card">
    <h3>Run Simulation</h3>
    <form id="simForm" onsubmit="return false;">
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        <div>
          <label class="small">Available Drivers</label>
          <input id="availableDrivers" type="number" value="5" min="1"/>
        </div>
        <div>
          <label class="small">Route Start Time (HH:MM)</label>
          <input id="routeStartTime" type="time" value="09:00"/>
        </div>
        <div>
          <label class="small">Max Hours per Driver</label>
          <input id="maxHours" type="number" value="8" min="1"/>
        </div>
      </div>
      <div style="margin-top:12px">
        <button id="runSim" class="btn">Run Simulation</button>
      </div>
    </form>
  </div>

  <div class="card" id="simResults" style="display:none">
    <h4>Simulation Results</h4>
    <div id="kpiSummary" class="grid cols-2"></div>
    <h5>Assignments (first 20)</h5>
    <div style="max-height:320px;overflow:auto">
      <table id="assignTable"><thead><tr><th>Order</th><th>Driver</th><th>ETA</th><th>Late</th><th>Profit</th></tr></thead><tbody></tbody></table>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  async function runSimulation() {
    const availableDrivers = parseInt(document.getElementById('availableDrivers').value) || 1;
    const routeStartTime = document.getElementById('routeStartTime').value || '09:00';
    const maxHours = parseInt(document.getElementById('maxHours').value) || 8;

    try {
      const res = await axios.post('/api/simulate', {
        available_drivers: availableDrivers,
        route_start_time: routeStartTime,
        max_hours_per_driver: maxHours
      });

      const payload = res.data.results ?? res.data;
      document.getElementById('simResults').style.display = 'block';

      const summary = document.getElementById('kpiSummary');
      summary.innerHTML = `
        <div class="card"><strong>Total Orders</strong><div>${payload.total_orders}</div></div>
        <div class="card"><strong>On-time</strong><div>${payload.on_time_deliveries}</div></div>
        <div class="card"><strong>Late</strong><div>${payload.late_deliveries}</div></div>
        <div class="card"><strong>Efficiency</strong><div>${payload.efficiency_score}%</div></div>
        <div class="card"><strong>Total Profit</strong><div>₹ ${Number(payload.total_profit).toFixed(2)}</div></div>
        <div class="card"><strong>Fuel Cost</strong><div>₹ ${Number(payload.total_fuel_cost).toFixed(2)}</div></div>
      `;

      const tbody = document.querySelector('#assignTable tbody');
      tbody.innerHTML = '';
      (payload.assignments || []).slice(0,20).forEach(a=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${a.order_code}</td><td>${a.driver || a.driver_id || 'N/A'}</td><td>${a.eta || 'N/A'}</td><td>${a.is_late ? 'Yes' : 'No'}</td><td>₹ ${Number(a.order_profit).toFixed(2)}</td>`;
        tbody.appendChild(tr);
      });

      // After run, dashboard will reflect saved simulation because backend persisted it.
    } catch (err) {
      console.error(err);
      alert('Simulation error: ' + (err.response?.data?.message || err.message));
    }
  }

  document.getElementById('runSim').addEventListener('click', runSimulation);
</script>
@endpush
