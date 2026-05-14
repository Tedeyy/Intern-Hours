<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../feed.php?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$current_month = (int)($_GET['month'] ?? date('m'));
$current_year = (int)($_GET['year'] ?? date('Y'));

$base_url = "../../../";
require_once '../../components/header.php';
?>

    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            padding: 20px;
        }

        .calendar-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 20px;
            color: #1a1a1a;
        }

        .calendar-nav {
            display: flex;
            gap: 10px;
        }

        .calendar-nav button {
            padding: 8px 12px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .calendar-nav button:hover {
            background: #4a4a4a;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .day-header {
            text-align: center;
            font-weight: 600;
            color: #666;
            padding: 10px;
            font-size: 12px;
        }

        .day-cell {
            aspect-ratio: 1;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .day-cell:hover {
            border-color: #1a1a1a;
            background: #f9f9f9;
        }

        .day-cell.other-month {
            background: #f5f5f5;
            color: #ccc;
        }

        .day-cell.today {
            border: 2px solid #1a1a1a;
            background: #f0f0f0;
        }

        .day-cell.logged {
            background: #e8f5e9;
            border-color: #4caf50;
        }

        .day-cell.disabled {
            background: #f5f5f5;
            color: #ccc;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .day-cell.disabled:hover {
            border-color: #ddd;
            background: #f5f5f5;
        }

        .day-cell-date {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 4px;
            color: #1a1a1a;
        }

        .day-cell-hours {
            font-size: 12px;
            color: #4caf50;
            font-weight: 600;
        }

        .stats-sidebar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .stat-unit {
            font-size: 12px;
            color: #999;
            margin-left: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a4a4a;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1a1a1a;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-save {
            background: #1a1a1a;
            color: white;
        }

        .btn-save:hover {
            background: #4a4a4a;
        }

        .btn-cancel {
            background: #ddd;
            color: #1a1a1a;
        }

        .btn-cancel:hover {
            background: #ccc;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background: #d32f2f;
        }

        .filter-section {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
        }

        .filter-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .filter-group input:focus {
            outline: none;
            border-color: #1a1a1a;
        }

        .filter-buttons {
            display: flex;
            gap: 8px;
        }

        .filter-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-filter {
            background: #1a1a1a;
            color: white;
        }

        .btn-filter:hover {
            background: #4a4a4a;
        }

        .btn-reset {
            background: #ddd;
            color: #1a1a1a;
        }

        .btn-reset:hover {
            background: #ccc;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group input {
                width: 100%;
            }

            .filter-buttons {
                width: 100%;
            }

            .filter-buttons button {
                flex: 1;
            }

            .calendar-grid {
                grid-template-columns: repeat(7, 1fr);
            }

            .day-cell {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <?php require_once '../../components/navbar.php'; ?>
    <div class="dashboard-container">
        <div class="calendar-section">

            <div class="calendar-header">
                <h2 id="calendar-title">December 2024</h2>
                <div class="calendar-nav">
                    <button onclick="previousMonth()">← Prev</button>
                    <button onclick="nextMonth()">Next →</button>
                </div>
            </div>

            <div class="calendar-grid" id="calendar-grid"></div>

            <div style="text-align: center; color: #666; font-size: 12px;">
                <p>Click a day to log or edit hours</p>
            </div>
        </div>

        <div class="stats-sidebar">
            <div class="stat-card">
                <div class="stat-label">Total Hours</div>
                <div class="stat-value">
                    <span id="total-hours">0</span>
                    <span class="stat-unit">hrs</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Month Total</div>
                <div class="stat-value">
                    <span id="month-total">0</span>
                    <span class="stat-unit">hrs</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Today's Hours</div>
                <div class="stat-value">
                    <span id="today-hours">0</span>
                    <span class="stat-unit">hrs</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Average/Day</div>
                <div class="stat-value">
                    <span id="average-hours">0</span>
                    <span class="stat-unit">hrs</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label" id="filtered-label">Filtered Total</div>
                <div class="stat-value">
                    <span id="filtered-total">0</span>
                    <span class="stat-unit">hrs</span>
                </div>
            </div>

            <div class="filter-section">
                <div class="stat-label">Filter by Date</div>
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" id="filter-from-date">
                </div>
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" id="filter-to-date">
                </div>
                <div class="filter-buttons">
                    <button class="btn-filter" onclick="applyFilter()">Apply</button>
                    <button class="btn-reset" onclick="resetFilter()">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Hours Modal -->
    <div class="modal" id="log-modal">
        <div class="modal-content">
            <div class="modal-header">Log Hours</div>
            <div class="form-group">
                <label>Date</label>
                <input type="text" id="modal-date" readonly style="background: #f5f5f5;">
            </div>
            <div class="form-group">
                <label>Hours Worked</label>
                <input type="number" id="modal-hours" min="0" max="24" step="0.5" placeholder="Enter hours">
            </div>
            <div class="modal-buttons">
                <button class="btn-save" onclick="saveHours()">Save</button>
                <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="btn-delete" id="delete-btn" style="display: none;" onclick="deleteHours()">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let currentMonth = parseInt('<?php echo $current_month; ?>');
        let currentYear = parseInt('<?php echo $current_year; ?>');
        let selectedDate = null;
        let hoursData = {};
        let monthHoursData = {};
        let allHoursData = {};
        let userId = parseInt('<?php echo $user_id; ?>');
        let filterFromDate = null;
        let filterToDate = null;

        // Initialize calendar
        document.addEventListener('DOMContentLoaded', function() {
            setDefaultDates();
            loadAllHours();
            loadHours();
            renderCalendar();
        });

        function setDefaultDates() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            
            document.getElementById('filter-from-date').valueAsDate = firstDay;
            document.getElementById('filter-to-date').valueAsDate = today;
        }

        function loadAllHours() {
            fetch('../../../api/hours.php?all=true')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allHoursData = data.hours;
                        updateTotalHours();
                    }
                })
                .catch(error => console.error('Error loading all hours:', error));
        }

        function renderCalendar() {
            const firstDay = new Date(currentYear, currentMonth - 1, 1);
            const lastDay = new Date(currentYear, currentMonth, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            
            let titleText = monthNames[currentMonth - 1] + ' ' + currentYear;
            if (filterFromDate && filterToDate) {
                titleText = 'Filtered (' + formatDate(filterFromDate) + ' to ' + formatDate(filterToDate) + ')';
            }
            
            document.getElementById('calendar-title').textContent = titleText;

            const calendarGrid = document.getElementById('calendar-grid');
            calendarGrid.innerHTML = '';

            // Day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                const header = document.createElement('div');
                header.className = 'day-header';
                header.textContent = day;
                calendarGrid.appendChild(header);
            });

            // Empty cells for days from previous month
            for (let i = 0; i < startingDayOfWeek; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'day-cell other-month';
                calendarGrid.appendChild(emptyCell);
            }

            // Days of current month
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const cell = document.createElement('div');
                const dateStr = String(day).padStart(2, '0');
                const fullDate = currentYear + '-' + String(currentMonth).padStart(2, '0') + '-' + dateStr;
                
                cell.className = 'day-cell';

                // Check if today
                if (day === today.getDate() && currentMonth === today.getMonth() + 1 && 
                    currentYear === today.getFullYear()) {
                    cell.classList.add('today');
                }

                // Check if date is in the future
                const cellDate = new Date(currentYear, currentMonth - 1, day);
                cellDate.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);
                const isFuture = cellDate > today;

                // Check if has logged hours
                if (hoursData[fullDate]) {
                    cell.classList.add('logged');
                }

                if (isFuture) {
                    cell.classList.add('disabled');
                }

                cell.innerHTML = `
                    <div class="day-cell-date">${day}</div>
                    ${hoursData[fullDate] ? `<div class="day-cell-hours">${hoursData[fullDate]}h</div>` : ''}
                `;

                if (!isFuture) {
                    cell.onclick = () => openLogModal(fullDate);
                }
                calendarGrid.appendChild(cell);
            }

            updateStats();
        }

        function loadHours() {
            fetch('../../../api/hours.php?month=' + currentMonth + '&year=' + currentYear)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hoursData = data.hours;
                        monthHoursData = JSON.parse(JSON.stringify(data.hours));
                        renderCalendar();
                    }
                })
                .catch(error => console.error('Error loading hours:', error));
        }

        function openLogModal(dateStr) {
            selectedDate = dateStr;
            document.getElementById('modal-date').value = dateStr;
            document.getElementById('modal-hours').value = hoursData[dateStr] || '';
            document.getElementById('delete-btn').style.display = hoursData[dateStr] ? 'block' : 'none';
            document.getElementById('log-modal').classList.add('active');
            document.getElementById('modal-hours').focus();
        }

        function closeModal() {
            document.getElementById('log-modal').classList.remove('active');
            selectedDate = null;
        }

        function saveHours() {
            const hours = document.getElementById('modal-hours').value;
            
            if (hours === '' || isNaN(hours) || parseFloat(hours) < 0) {
                alert('Please enter a valid number of hours');
                return;
            }

            const formData = new FormData();
            formData.append('date', selectedDate);
            formData.append('hours', hours);

            fetch('../../../api/hours.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const parsedHours = parseFloat(hours);
                    hoursData[selectedDate] = parsedHours;
                    monthHoursData[selectedDate] = parsedHours;
                    renderCalendar();
                    closeModal();
                } else {
                    alert(data.error || 'Error saving hours');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving hours');
            });
        }

        function deleteHours() {
            if (!confirm('Are you sure you want to delete this entry?')) return;

            const formData = new FormData();
            formData.append('date', selectedDate);
            formData.append('delete', 'true');

            fetch('../../../api/hours.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    delete hoursData[selectedDate];
                    delete monthHoursData[selectedDate];
                    renderCalendar();
                    closeModal();
                } else {
                    alert(data.error || 'Error deleting entry');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting entry');
            });
        }

        function previousMonth() {
            let newMonth = currentMonth - 1;
            let newYear = currentYear;
            
            if (newMonth < 1) {
                newMonth = 12;
                newYear--;
            }
            
            window.location.href = '?month=' + newMonth + '&year=' + newYear;
        }

        function nextMonth() {
            let newMonth = currentMonth + 1;
            let newYear = currentYear;
            
            if (newMonth > 12) {
                newMonth = 1;
                newYear++;
            }
            
            window.location.href = '?month=' + newMonth + '&year=' + newYear;
        }

        function updateStats() {
            const monthTotal = Object.values(monthHoursData).reduce((sum, val) => sum + parseFloat(val), 0);
            document.getElementById('month-total').textContent = monthTotal.toFixed(1);

            // Today's hours
            const today = new Date();
            const todayStr = today.getFullYear() + '-' + 
                String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                String(today.getDate()).padStart(2, '0');
            document.getElementById('today-hours').textContent = (hoursData[todayStr] || 0).toFixed(1);

            // Average
            const daysLogged = Object.keys(monthHoursData).length;
            const average = daysLogged > 0 ? (monthTotal / daysLogged) : 0;
            document.getElementById('average-hours').textContent = average.toFixed(1);
        }

        function updateTotalHours() {
            const total = Object.values(allHoursData).reduce((sum, val) => sum + parseFloat(val), 0);
            document.getElementById('total-hours').textContent = total.toFixed(1);
        }

        function applyFilter() {
            const fromDate = document.getElementById('filter-from-date').value;
            const toDate = document.getElementById('filter-to-date').value;
            
            if (!fromDate || !toDate) {
                alert('Please select both dates');
                return;
            }
            
            if (fromDate > toDate) {
                alert('From date must be before to date');
                return;
            }
            
            filterFromDate = fromDate;
            filterToDate = toDate;
            
            loadFilteredHours();
        }

        function resetFilter() {
            filterFromDate = null;
            filterToDate = null;
            setDefaultDates();
            loadAllHours();
            document.getElementById('filtered-total').textContent = '0';
            document.getElementById('filtered-label').textContent = 'Filtered Total';
            renderCalendar();
        }

        function loadFilteredHours() {
            const params = new URLSearchParams();
            params.append('from_date', filterFromDate);
            params.append('to_date', filterToDate);
            
            fetch('../../../api/hours.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hoursData = data.hours;
                        updateFilteredTotal();
                        renderCalendar();
                    }
                })
                .catch(error => console.error('Error loading filtered hours:', error));
        }

        function updateFilteredTotal() {
            const filteredTotal = Object.values(hoursData).reduce((sum, val) => sum + parseFloat(val), 0);
            document.getElementById('filtered-total').textContent = filteredTotal.toFixed(1);
            
            if (filterFromDate && filterToDate) {
                const formattedFrom = formatDate(filterFromDate);
                const formattedTo = formatDate(filterToDate);
                document.getElementById('filtered-label').textContent = `${formattedFrom} to ${formattedTo} Total`;
            } else {
                document.getElementById('filtered-label').textContent = 'Filtered Total';
            }
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const [y, m, d] = dateStr.split('-');
            return `${months[parseInt(m) - 1]} ${parseInt(d)}, ${y}`;
        }

        // Close modal on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Close modal on outside click
        document.getElementById('log-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    <?php require_once '../../components/footer.php'; ?>
</body>
</html>
