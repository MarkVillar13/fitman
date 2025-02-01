<script>
  function generateCalendar(year, month) {
    const calendarTable = document.getElementById("calendar").getElementsByTagName('tbody')[0];
    calendarTable.innerHTML = "";

    const currentDate = new Date();
    const currentDay = currentDate.getDate();
    const firstDay = new Date(year, month - 1, 1);
    const lastDay = new Date(year, month, 0);
    const daysInMonth = lastDay.getDate();

    let dayCounter = 1;
    for (let i = 0; i < 6; i++) {
      const row = calendarTable.insertRow(i);
      for (let j = 0; j < 7; j++) {
        const cell = row.insertCell(j);
        if ((i === 0 && j < firstDay.getDay()) || dayCounter > daysInMonth) {
          cell.textContent = "";
        } else {
          cell.textContent = dayCounter;
          cell.classList.add("w3-center");
          if (dayCounter === currentDay) {
            cell.classList.add("today");
          }
          dayCounter++;
        }
      }
    }

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    document.getElementById("calendar-header").textContent = `${monthNames[month - 1]} ${year}`;
  }

  // Example usage for the current month and year
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth() + 1; // Months are 0-indexed

  generateCalendar(currentYear, currentMonth);
</script>
