document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchTeacher');
    const searchResults = document.getElementById('searchResults');

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const query = searchInput.value;

        fetch(`search_teachers.php?q=${query}`)
            .then(response => response.json())
            .then(teachers => {
                searchResults.innerHTML = '';
                if (teachers.length > 0) {
                    teachers.forEach(teacher => {
                        const teacherDiv = document.createElement('div');
                        teacherDiv.innerHTML = `
                            <h3>${teacher.name} (${teacher.subject})</h3>
                            <button onclick="bookAppointment(${teacher.teacher_id})">View Availability</button>
                            <div id="availability-${teacher.teacher_id}"></div>
                        `;
                        searchResults.appendChild(teacherDiv);
                    });
                } else {
                    searchResults.innerHTML = '<p>No teachers found.</p>';
                }
            });
    });
});

function bookAppointment(teacherId) {
    fetch(`get_availability.php?teacher_id=${teacherId}`)
        .then(response => response.json())
        .then(slots => {
            const availabilityDiv = document.getElementById(`availability-${teacherId}`);
            availabilityDiv.innerHTML = '';
            if (slots.length > 0) {
                slots.forEach(slot => {
                    const slotDiv = document.createElement('div');
                    slotDiv.innerHTML = `
                        <p>${slot.start_time} to ${slot.end_time}</p>
                        <button onclick="confirmBooking(${teacherId}, ${slot.availability_id})">Book</button>
                    `;
                    availabilityDiv.appendChild(slotDiv);
                });
            } else {
                availabilityDiv.innerHTML = '<p>No available slots.</p>';
            }
        });
}

function confirmBooking(teacherId, availabilityId) {
    fetch('book_appointment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ teacherId, availabilityId })
    })
    .then(response => response.json())
    .then(result => {
        alert(result.message);
        window.location.reload(); // Reload to see new appointment
    });
}