// Global variables will be initialized in the PHP file
// currentMonth, currentYear, selectedDate, hoursData, monthHoursData, allHoursData, userId, filterFromDate, filterToDate

// Initialize calendar
document.addEventListener('DOMContentLoaded', function() {
    setDefaultDates();
    loadAllHours();
    loadAbsences();
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
            ${absencesData[fullDate] ? `<div class="absence-badge ${absencesData[fullDate].status.toLowerCase()}">${absencesData[fullDate].status}</div>` : ''}
        `;

        if (!isFuture) {
            cell.onclick = () => openLogModal(fullDate);
        } else {
            cell.onclick = () => openAbsenceModal(fullDate);
        }
        calendarGrid.appendChild(cell);
    }

    updateStats();
}

function loadAbsences() {
    fetch('../../../api/absences.php?month=' + currentMonth + '&year=' + currentYear)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                absencesData = {};
                data.absences.forEach(abs => {
                    absencesData[abs.date] = abs;
                });
                renderCalendar();
            }
        })
        .catch(error => console.error('Error loading absences:', error));
}

function openAbsenceModal(dateStr) {
    selectedDate = dateStr;
    const absence = absencesData[dateStr];
    const statusDisplay = document.getElementById('absence-status-display');
    const deleteBtn = document.getElementById('absence-delete-btn');
    const submitBtn = document.getElementById('absence-submit-btn');

    document.getElementById('absence-modal-date').value = dateStr;
    document.getElementById('absence-modal-reason').value = absence ? absence.reason : '';

    if (absence) {
        statusDisplay.textContent = 'Status: ' + absence.status;
        statusDisplay.className = 'absence-badge ' + absence.status.toLowerCase();
        statusDisplay.style.display = 'block';
        statusDisplay.style.fontSize = '14px';
        statusDisplay.style.padding = '10px';
        deleteBtn.style.display = 'block';
        submitBtn.textContent = 'Update Reason';
    } else {
        statusDisplay.style.display = 'none';
        deleteBtn.style.display = 'none';
        submitBtn.textContent = 'Submit Request';
    }

    document.getElementById('absence-modal').classList.add('active');
    document.getElementById('absence-modal-reason').focus();
}

function closeAbsenceModal() {
    document.getElementById('absence-modal').classList.remove('active');
    selectedDate = null;
}

function saveAbsence() {
    const reason = document.getElementById('absence-modal-reason').value;
    
    if (reason.trim() === '') {
        alert('Please provide a reason for your absence');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'apply');
    formData.append('date', selectedDate);
    formData.append('reason', reason);

    fetch('../../../api/absences.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadAbsences();
            closeAbsenceModal();
        } else {
            alert(data.error || 'Error submitting request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting request');
    });
}

function deleteAbsence() {
    if (!confirm('Are you sure you want to cancel this absence request?')) return;

    const absence = absencesData[selectedDate];
    if (!absence) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', absence.absences_id);

    fetch('../../../api/absences.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            delete absencesData[selectedDate];
            loadAbsences();
            closeAbsenceModal();
        } else {
            alert(data.error || 'Error deleting request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting request');
    });
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

document.getElementById('absence-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAbsenceModal();
    }
});
