function updateTime() {
  const now = new Date();
  const options = { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
  document.getElementById('timedate').textContent = now.toLocaleTimeString([], options);

  // Highlight current period
  const rows = document.querySelectorAll('.timetable tr[data-start]');
  const currentMinutes = now.getHours() * 60 + now.getMinutes();

  rows.forEach(row => {
    const [startH, startM] = row.dataset.start.split(':').map(Number);
    const [endH, endM] = row.dataset.end.split(':').map(Number);
    const startMinutes = startH * 60 + startM;
    const endMinutes = endH * 60 + endM;

    if (currentMinutes >= startMinutes && currentMinutes <= endMinutes) {
      row.classList.add('current-period');
    } else {
      row.classList.remove('current-period');
    }
  });
}

updateTime();
setInterval(updateTime, 1000);