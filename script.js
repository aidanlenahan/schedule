schedule.forEach(day => {
  console.log(`${day.date} - ${day.day} - Half Day: ${day.halfday} - Delayed Opening: ${day.delayedOpening}`);
});

function updateSchedule() {
  const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
  const todaySchedule = schedule.find(entry => entry.date === today);

  if (todaySchedule) {
    // 1. Swap the image
    const img = document.querySelector(".bigbuc");
    const dayLetter = todaySchedule.day.charAt(0).toLowerCase(); // a, b, c, d
    img.src = `img/${dayLetter}day.png`;
    img.alt = todaySchedule.day;

    // 2. Reorder the timetable rows
    const table = document.querySelector(".timetable");
    const headerRow = table.querySelector("tr"); // keep header
    const allRows = Array.from(table.querySelectorAll("tr[data-start]"));

    // Map period name -> row element
    const rowMap = {};
    allRows.forEach(row => {
      const periodName = row.querySelector("td").textContent.trim();
      rowMap[periodName] = row;
    });

    // Clear table except header
    table.innerHTML = "";
    table.appendChild(headerRow);

    // Append rows in the order you want (you can adjust if using JSON without blocks)
    // For now, just re-append all rows in current HTML order
    allRows.forEach(row => table.appendChild(row));
  } else {
    console.warn("No schedule for today:", today);
  }
}



// Optional: keep refreshing once a day or every hour
setInterval(updateSchedule, 60 * 60 * 1000);


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

console.log(schedule);