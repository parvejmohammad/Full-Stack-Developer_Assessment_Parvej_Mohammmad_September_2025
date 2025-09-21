@extends('layout')

@section('content')
  <div class="card">
    <h3>Management</h3>
    <div style="display:flex;gap:12px;margin-top:8px">
      <button class="btn" onclick="showTab('drivers')">Drivers</button>
      <button class="btn" onclick="showTab('routes')">Routes</button>
      <button class="btn" onclick="showTab('orders')">Orders</button>
    </div>
  </div>

  <div id="driversTab" class="card">
    <h4>Drivers</h4>
    <form id="driverForm" onsubmit="return false" style="display:flex;gap:8px">
      <input id="driverName" placeholder="Name" />
      <input id="driverShift" placeholder="Shift Hours" type="number" />
      <button class="btn" onclick="createDriver()">Create</button>
    </form>
    <div style="margin-top:12px"><table id="driversTable"><thead><tr><th>Name</th><th>Shift Hours</th><th>Actions</th></tr></thead><tbody></tbody></table></div>
  </div>

  <div id="routesTab" class="card" style="display:none">
    <h4>Routes</h4>
    <form id="routeForm" onsubmit="return false" style="display:flex;gap:8px">
      <input id="routeCode" placeholder="Route Code or ID" />
      <input id="routeDistance" placeholder="Distance km" type="number" />
      <select id="routeTraffic">
        <option>Low</option><option>Medium</option><option>High</option>
      </select>
      <input id="routeBase" placeholder="Base time (min)" type="number" />
      <button class="btn" onclick="createRoute()">Create</button>
    </form>
    <div style="margin-top:12px"><table id="routesTable"><thead><tr><th>Code</th><th>Distance</th><th>Traffic</th><th>Base Time</th><th>Actions</th></tr></thead><tbody></tbody></table></div>
  </div>

  <div id="ordersTab" class="card" style="display:none">
    <h4>Orders</h4>
    <form id="orderForm" onsubmit="return false" style="display:flex;gap:8px;flex-wrap:wrap">
      <input id="orderCode" placeholder="Order Code" />
      <input id="orderValue" placeholder="Value (₹)" type="number" step="0.01"/>
      <select id="orderRoute"></select>
      <input id="orderTime" placeholder="Delivery time (HH:MM)" type="time"/>
      <button class="btn" onclick="createOrder()">Create</button>
    </form>
    <div style="margin-top:12px"><table id="ordersTable"><thead><tr><th>Order</th><th>Value</th><th>Route</th><th>Delivery Min</th><th>Actions</th></tr></thead><tbody></tbody></table></div>
  </div>
@endsection

@push('scripts')
<script>
  // show tab UI
  function showTab(name){
    document.getElementById('driversTab').style.display = name==='drivers' ? 'block' : 'none';
    document.getElementById('routesTab').style.display = name==='routes' ? 'block' : 'none';
    document.getElementById('ordersTab').style.display = name==='orders' ? 'block' : 'none';
    if (name==='drivers') loadDrivers();
    if (name==='routes') loadRoutes();
    if (name==='orders') { loadRoutes(); loadOrders(); }
  }

  // Drivers
  async function loadDrivers(){
    try {
      const res = await axios.get('/api/drivers');
      const list = res.data || [];
      const tbody = document.querySelector('#driversTable tbody'); tbody.innerHTML = '';
      list.forEach(d=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${d.name}</td><td>${d.current_shift_hours ?? 0}</td>
          <td class="actions"><button onclick="deleteDriver(${d.id})" class="btn secondary small">Delete</button></td>`;
        tbody.appendChild(tr);
      });
    } catch(err){ console.error(err); alert('Load drivers failed'); }
  }
  async function createDriver(){
    const name = document.getElementById('driverName').value;
    const shift = Number(document.getElementById('driverShift').value) || 0;
    if(!name) return alert('Name required');
    await axios.post('/api/drivers', { name, current_shift_hours: shift });
    document.getElementById('driverName').value=''; document.getElementById('driverShift').value='';
    loadDrivers();
  }
  async function deleteDriver(id){
    if(!confirm('Delete driver?')) return;
    await axios.delete('/api/drivers/' + id);
    loadDrivers();
  }

  // Routes
  async function loadRoutes(){
    try {
      const res = await axios.get('/api/routes');
      const list = res.data || [];
      const tbody = document.querySelector('#routesTable tbody'); tbody.innerHTML = '';
      const sel = document.getElementById('orderRoute'); sel.innerHTML = '';
      list.forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.route_code}</td><td>${r.distance_km}</td><td>${r.traffic_level}</td><td>${r.base_time_minutes}</td>
          <td class="actions"><button onclick="deleteRoute(${r.id})" class="btn secondary small">Delete</button></td>`;
        tbody.appendChild(tr);
        const opt = document.createElement('option'); opt.value = r.id; opt.textContent = r.route_code; sel.appendChild(opt);
      });
    } catch(err){ console.error(err); alert('Load routes failed'); }
  }
  async function createRoute(){
    const route_code = document.getElementById('routeCode').value;
    const distance_km = Number(document.getElementById('routeDistance').value) || 0;
    const traffic_level = document.getElementById('routeTraffic').value;
    const base_time_minutes = Number(document.getElementById('routeBase').value) || 1;
    if(!route_code) return alert('route code required');
    await axios.post('/api/routes', { route_code, distance_km, traffic_level, base_time_minutes });
    document.getElementById('routeCode').value=''; document.getElementById('routeDistance').value=''; document.getElementById('routeBase').value='';
    loadRoutes();
  }
  async function deleteRoute(id){
    if(!confirm('Delete route?')) return;
    await axios.delete('/api/routes/' + id);
    loadRoutes();
  }

  // Orders
  async function loadOrders(){
    try {
      const res = await axios.get('/api/orders');
      const list = res.data || [];
      const tbody = document.querySelector('#ordersTable tbody'); tbody.innerHTML = '';
      list.forEach(o=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${o.order_code}</td><td>₹ ${Number(o.value_rs).toFixed(2)}</td><td>${o.route?.route_code ?? 'N/A'}</td><td>${o.delivery_minutes ?? 'N/A'}</td>
          <td class="actions"><button onclick="deleteOrder(${o.id})" class="btn secondary small">Delete</button></td>`;
        tbody.appendChild(tr);
      });
    } catch(err){ console.error(err); alert('Load orders failed'); }
  }
  async function createOrder(){
    const order_code = document.getElementById('orderCode').value;
    const value_rs = Number(document.getElementById('orderValue').value) || 0;
    const delivery_route_id = Number(document.getElementById('orderRoute').value) || null;
    const time = document.getElementById('orderTime').value || '00:00';
    const [hh,mm] = time.split(':'); const delivery_minutes = Number(hh)*60 + Number(mm);
    if(!order_code) return alert('order code required');
    await axios.post('/api/orders', { order_code, delivery_route_id, value_rs, delivery_minutes });
    document.getElementById('orderCode').value=''; document.getElementById('orderValue').value='';
    loadOrders();
  }
  async function deleteOrder(id){
    if(!confirm('Delete order?')) return;
    await axios.delete('/api/orders/' + id);
    loadOrders();
  }

  // initialize default tab
  showTab('drivers');
</script>
@endpush
