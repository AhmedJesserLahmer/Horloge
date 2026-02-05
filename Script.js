let currentMonth = new Date();
let selectedDate = new Date();
let showAnalogClock = false;
let notes = [];
let reminders = [];
let checklistItems = [];
let currentPriority = 'medium';

const monthNames = [
    "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
];

document.addEventListener('DOMContentLoaded', function() {
    initCalendar();
    initClock();
    initNotes();
    initReminders();
    initChecklist();
    initAnalogClock();
    updateDateInfo();
});

function initCalendar() {
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1);
        renderCalendar();
    });

    renderCalendar();
}

function renderCalendar() {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();

    document.getElementById('currentMonth').textContent = 
        `${monthNames[month]} ${year}`;

    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    for (let i = 0; i < firstDay; i++) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'calendar-day';
        grid.appendChild(emptyDiv);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'calendar-day';

        const button = document.createElement('button');
        button.className = 'calendar-day-btn';
        button.textContent = day;

        const today = new Date();
        if (day === today.getDate() && 
            month === today.getMonth() && 
            year === today.getFullYear()) {
            button.classList.add('today');
        }

        if (selectedDate && 
            day === selectedDate.getDate() && 
            month === selectedDate.getMonth() && 
            year === selectedDate.getFullYear()) {
            button.classList.add('selected');
        }

        button.addEventListener('click', () => {
            selectedDate = new Date(year, month, day);
            renderCalendar();
            updateDateInfo();
            renderReminders();
        });

        dayDiv.appendChild(button);
        grid.appendChild(dayDiv);
    }
}

function initClock() {
    updateClock();
    setInterval(updateClock, 1000);

    document.getElementById('toggleClock').addEventListener('click', () => {
        showAnalogClock = !showAnalogClock;
        const container = document.getElementById('analogClockContainer');
        const btn = document.getElementById('toggleClock');

        if (showAnalogClock) {
            container.classList.remove('hidden');
            btn.textContent = 'Afficher horloge numérique';
        } else {
            container.classList.add('hidden');
            btn.textContent = 'Afficher horloge analogique';
        }
    });
}

function updateClock() {
    const now = new Date();
    const tunisiaTime = new Date(now.toLocaleString("en-US", { timeZone: "Africa/Tunis" }));

    const hours = String(tunisiaTime.getHours()).padStart(2, '0');
    const minutes = String(tunisiaTime.getMinutes()).padStart(2, '0');
    const seconds = String(tunisiaTime.getSeconds()).padStart(2, '0');

    document.getElementById('digitalTime').textContent = `${hours}:${minutes}:${seconds}`;

    const dateOptions = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'Africa/Tunis'
    };
    document.getElementById('digitalDate').textContent = 
        tunisiaTime.toLocaleDateString('fr-FR', dateOptions);

    if (showAnalogClock) {
        updateAnalogClock(tunisiaTime);
    }
}

function initAnalogClock() {
    const markersContainer = document.getElementById('clockMarkers');
    const numbersContainer = document.getElementById('clockNumbers');

    for (let i = 0; i < 12; i++) {
        const angle = (i * 30 * Math.PI) / 180;
        const x = 50 + 38 * Math.sin(angle);
        const y = 50 - 38 * Math.cos(angle);

        const marker = document.createElement('div');
        marker.className = 'clock-marker';
        marker.style.left = `${x}%`;
        marker.style.top = `${y}%`;
        markersContainer.appendChild(marker);
    }

    [12, 3, 6, 9].forEach(num => {
        const angle = ((num === 12 ? 0 : num * 30) * Math.PI) / 180;
        const x = 50 + 42 * Math.sin(angle);
        const y = 50 - 42 * Math.cos(angle);

        const number = document.createElement('div');
        number.className = 'clock-number';
        number.textContent = num;
        number.style.left = `${x}%`;
        number.style.top = `${y}%`;
        numbersContainer.appendChild(number);
    });
}

function updateAnalogClock(time) {
    const seconds = time.getSeconds();
    const minutes = time.getMinutes();
    const hours = time.getHours() % 12;

    const secondDegrees = (seconds / 60) * 360;
    const minuteDegrees = (minutes / 60) * 360 + (seconds / 60) * 6;
    const hourDegrees = (hours / 12) * 360 + (minutes / 60) * 30;

    document.getElementById('secondHand').style.transform = 
        `translate(-50%, 0) rotate(${secondDegrees}deg)`;
    document.getElementById('minuteHand').style.transform = 
        `translate(-50%, 0) rotate(${minuteDegrees}deg)`;
    document.getElementById('hourHand').style.transform = 
        `translate(-50%, 0) rotate(${hourDegrees}deg)`;
}

function updateDateInfo() {
    if (!selectedDate) {
        document.getElementById('dateInfo').innerHTML = `
            <div class="empty-state">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p>Sélectionnez une date dans le calendrier</p>
            </div>
        `;
        return;
    }

    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    };
    const fullDate = selectedDate.toLocaleDateString('fr-FR', options);

    const dayOfYear = getDayOfYear(selectedDate);
    const weekNumber = getWeekNumber(selectedDate);
    const daysUntil = getDaysUntil(selectedDate);

    let daysUntilText = '';
    let daysUntilValue = '';
    if (daysUntil === 0) {
        daysUntilText = "Aujourd'hui";
        daysUntilValue = '✓';
    } else if (daysUntil > 0) {
        daysUntilText = "Jours restants";
        daysUntilValue = `${daysUntil} jours`;
    } else {
        daysUntilText = "Jours passés";
        daysUntilValue = `${Math.abs(daysUntil)} jours`;
    }

    document.getElementById('dateInfo').innerHTML = `
        <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; margin-bottom: 1rem;">
            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Date complète</p>
            <p style="font-size: 1.125rem; color: #1f2937; font-weight: 500;">${fullDate}</p>
        </div>

        <div class="date-info-grid">
            <div class="info-box blue">
                <p class="info-label">Jour de l'année</p>
                <p class="info-value blue">${dayOfYear}</p>
            </div>
            <div class="info-box purple">
                <p class="info-label">Semaine</p>
                <p class="info-value purple">${weekNumber}</p>
            </div>
        </div>

        <div class="info-box cyan" style="margin-bottom: 1rem;">
            <p class="info-label">${daysUntilText}</p>
            <p class="info-value cyan">${daysUntilValue}</p>
        </div>

        <div style="display: flex; align-items: center; color: #4b5563; margin-bottom: 0.5rem;">
            <svg class="icon" style="color: #ef4444; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Tunisie, Afrique du Nord</span>
        </div>

        <div style="display: flex; align-items: center; color: #4b5563;">
            <svg class="icon" style="color: #10b981; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Fuseau horaire: UTC+1 (CET)</span>
        </div>
    `;
}

function getDayOfYear(date) {
    const start = new Date(date.getFullYear(), 0, 0);
    const diff = date - start;
    const oneDay = 1000 * 60 * 60 * 24;
    return Math.floor(diff / oneDay);
}

function getWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

function getDaysUntil(date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const target = new Date(date);
    target.setHours(0, 0, 0, 0);
    const diff = target - today;
    return Math.ceil(diff / (1000 * 60 * 60 * 24));
}

function initNotes() {
    document.getElementById('addNoteBtn').addEventListener('click', () => {
        document.getElementById('noteForm').classList.remove('hidden');
        document.getElementById('addNoteBtn').classList.add('hidden');
    });

    document.getElementById('cancelNoteBtn').addEventListener('click', () => {
        document.getElementById('noteForm').classList.add('hidden');
        document.getElementById('addNoteBtn').classList.remove('hidden');
        document.getElementById('noteTitle').value = '';
        document.getElementById('noteContent').value = '';
    });

    document.getElementById('saveNoteBtn').addEventListener('click', saveNote);

    renderNotes();
}

function saveNote() {
    const title = document.getElementById('noteTitle').value.trim();
    const content = document.getElementById('noteContent').value.trim();

    if (title || content) {
        notes.push({
            id: Date.now().toString(),
            title: title || 'Note sans titre',
            content: content,
            date: new Date().toISOString()
        });

        document.getElementById('noteTitle').value = '';
        document.getElementById('noteContent').value = '';
        document.getElementById('noteForm').classList.add('hidden');
        document.getElementById('addNoteBtn').classList.remove('hidden');

        renderNotes();
    }
}

function deleteNote(id) {
    notes = notes.filter(note => note.id !== id);
    renderNotes();
}

function renderNotes() {
    const container = document.getElementById('notesList');

    if (notes.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <p>Aucune note pour le moment</p>
            </div>
        `;
        return;
    }

    container.innerHTML = notes.map(note => `
        <div class="note-item">
            <div class="item-header">
                <h4 class="item-title">${note.title}</h4>
                <button class="delete-btn" onclick="deleteNote('${note.id}')">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            <p class="item-content">${note.content}</p>
            <p class="item-date">${formatDate(note.date)}</p>
        </div>
    `).join('');
}

function initReminders() {
    document.getElementById('addReminderBtn').addEventListener('click', () => {
        document.getElementById('reminderForm').classList.remove('hidden');
        document.getElementById('addReminderBtn').classList.add('hidden');
    });

    document.getElementById('cancelReminderBtn').addEventListener('click', () => {
        document.getElementById('reminderForm').classList.add('hidden');
        document.getElementById('addReminderBtn').classList.remove('hidden');
        clearReminderForm();
    });

    document.getElementById('saveReminderBtn').addEventListener('click', saveReminder);

    renderReminders();
}

function saveReminder() {
    const title = document.getElementById('reminderTitle').value.trim();
    const date = document.getElementById('reminderDate').value;
    const time = document.getElementById('reminderTime').value;
    const description = document.getElementById('reminderDescription').value.trim();

    if (title && date && time) {
        reminders.push({
            id: Date.now().toString(),
            title,
            date,
            time,
            description
        });

        clearReminderForm();
        document.getElementById('reminderForm').classList.add('hidden');
        document.getElementById('addReminderBtn').classList.remove('hidden');

        renderReminders();
    }
}

function clearReminderForm() {
    document.getElementById('reminderTitle').value = '';
    document.getElementById('reminderDate').value = '';
    document.getElementById('reminderTime').value = '';
    document.getElementById('reminderDescription').value = '';
}

function deleteReminder(id) {
    reminders = reminders.filter(reminder => reminder.id !== id);
    renderReminders();
}

function renderReminders() {
   
    const sortedReminders = [...reminders].sort((a, b) => {
        const dateA = new Date(`${a.date}T${a.time}`);
        const dateB = new Date(`${b.date}T${b.time}`);
        return dateA - dateB;
    });

    
    const upcomingReminders = sortedReminders.filter(reminder => {
        const reminderDateTime = new Date(`${reminder.date}T${reminder.time}`);
        return reminderDateTime >= new Date();
    });

    
    const selectedDateReminders = selectedDate ? sortedReminders.filter(reminder => {
        const reminderDate = new Date(reminder.date);
        return reminderDate.getDate() === selectedDate.getDate() &&
               reminderDate.getMonth() === selectedDate.getMonth() &&
               reminderDate.getFullYear() === selectedDate.getFullYear();
    }) : [];

    
    const selectedContainer = document.getElementById('selectedDateReminders');
    if (selectedDateReminders.length > 0) {
        const dateStr = selectedDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
        selectedContainer.innerHTML = `
            <div class="selected-date-reminders">
                <div style="display: flex; align-items: center; margin-bottom: 0.75rem;">
                    <svg class="icon" style="color: #3b82f6; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h4 style="font-weight: 600; color: #1f2937;">Rappels pour ${dateStr}</h4>
                </div>
                ${selectedDateReminders.map(reminder => `
                    <div style="padding: 0.75rem; margin-bottom: 0.5rem; background: white; border: 1px solid #3b82f6; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p style="font-weight: 500; color: #1f2937;">${reminder.title}</p>
                            <p style="font-size: 0.875rem; color: #4b5563;">${reminder.time}</p>
                            ${reminder.description ? `<p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">${reminder.description}</p>` : ''}
                        </div>
                        <button class="delete-btn" onclick="deleteReminder('${reminder.id}')">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                `).join('')}
            </div>
        `;
    } else {
        selectedContainer.innerHTML = '';
    }

    
    const container = document.getElementById('remindersList');
    if (upcomingReminders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p>Aucun rappel à venir</p>
            </div>
        `;
        return;
    }

    container.innerHTML = upcomingReminders.map(reminder => `
        <div class="reminder-item">
            <div class="item-header">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <svg class="icon" style="width: 16px; height: 16px; color: #ef4444; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <h4 class="item-title">${reminder.title}</h4>
                    </div>
                    <p style="font-size: 0.875rem; color: #4b5563; margin-bottom: 0.25rem;">${formatDateTime(reminder.date, reminder.time)}</p>
                    ${reminder.description ? `<p style="font-size: 0.875rem; color: #4b5563;">${reminder.description}</p>` : ''}
                </div>
                <button class="delete-btn" onclick="deleteReminder('${reminder.id}')">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
}


function initChecklist() {
    const priorityButtons = document.querySelectorAll('.priority-btn');
    priorityButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            priorityButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentPriority = btn.dataset.priority;
        });
    });

    document.getElementById('addChecklistBtn').addEventListener('click', addChecklistItem);
    document.getElementById('checklistInput').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            addChecklistItem();
        }
    });

    renderChecklist();
}

function addChecklistItem() {
    const input = document.getElementById('checklistInput');
    const text = input.value.trim();

    if (text) {
        checklistItems.push({
            id: Date.now().toString(),
            text,
            completed: false,
            priority: currentPriority
        });

        input.value = '';
        renderChecklist();
    }
}

function toggleChecklistItem(id) {
    const item = checklistItems.find(i => i.id === id);
    if (item) {
        item.completed = !item.completed;
        renderChecklist();
    }
}

function deleteChecklistItem(id) {
    checklistItems = checklistItems.filter(item => item.id !== id);
    renderChecklist();
}

function renderChecklist() {
    const completedItems = checklistItems.filter(item => item.completed);
    const pendingItems = checklistItems.filter(item => !item.completed);

    document.getElementById('completedCount').textContent = completedItems.length;
    document.getElementById('totalCount').textContent = checklistItems.length;

    
    const progressBar = document.getElementById('progressBar');
    if (checklistItems.length > 0) {
        progressBar.classList.remove('hidden');
        const percentage = Math.round((completedItems.length / checklistItems.length) * 100);
        document.getElementById('progressPercent').textContent = `${percentage}%`;
        document.getElementById('progressFill').style.width = `${percentage}%`;
    } else {
        progressBar.classList.add('hidden');
    }

    
    const container = document.getElementById('checklistItems');

    if (checklistItems.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p>Aucune tâche pour le moment</p>
            </div>
        `;
        return;
    }

    let html = '';

    
    if (pendingItems.length > 0) {
        html += pendingItems.map(item => {
            const priorityClass = item.priority === 'high' ? 'high-priority' : 
                                 item.priority === 'medium' ? 'medium-priority' : '';
            const priorityLabel = item.priority === 'high' ? 'Haute' :
                                 item.priority === 'medium' ? 'Moyenne' : 'Basse';
            
            return `
                <div class="checklist-item ${priorityClass}">
                    <div class="checklist-content">
                        <input type="checkbox" class="checkbox" onchange="toggleChecklistItem('${item.id}')">
                        <div class="checklist-text">
                            <p style="margin-bottom: 0.5rem;">${item.text}</p>
                            <span class="priority-badge ${item.priority}">${priorityLabel}</span>
                        </div>
                        <button class="delete-btn" onclick="deleteChecklistItem('${item.id}')">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    
    if (completedItems.length > 0) {
        html += `
            <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb; margin-top: 1rem;">
                <h4 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; margin-bottom: 0.75rem;">
                    Complétées (${completedItems.length})
                </h4>
        `;

        html += completedItems.map(item => `
            <div class="checklist-item completed">
                <div class="checklist-content">
                    <input type="checkbox" class="checkbox" checked onchange="toggleChecklistItem('${item.id}')">
                    <div class="checklist-text completed">
                        <p>${item.text}</p>
                    </div>
                    <button class="delete-btn" onclick="deleteChecklistItem('${item.id}')">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');

        html += '</div>';
    }

    container.innerHTML = html;
}


function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDateTime(dateStr, timeStr) {
    const date = new Date(`${dateStr}T${timeStr}`);
    return date.toLocaleDateString('fr-FR', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}