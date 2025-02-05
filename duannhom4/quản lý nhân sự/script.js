document.getElementById('employee-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const name = document.getElementById('name').value;
    const dob = document.getElementById('dob').value;
    const idCard = document.getElementById('id_card').value;
    const position = document.getElementById('position').value;
    const department = document.getElementById('department').value;

    const table = document.getElementById('employee-table').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();

    newRow.insertCell(0).textContent = name;
    newRow.insertCell(1).textContent = dob;
    newRow.insertCell(2).textContent = idCard;
    newRow.insertCell(3).textContent = position;
    newRow.insertCell(4).textContent = department;

    const actionsCell = newRow.insertCell(5);
    actionsCell.innerHTML = `<button class="edit">Chỉnh Sửa</button> <button class="delete">Xóa</button>`;

    document.getElementById('employee-form').reset();
});

document.getElementById('employee-table').addEventListener('click', function(event) {
    const target = event.target;

    if (target.classList.contains('delete')) {
        const row = target.closest('tr');
        row.remove();
    }

    if (target.classList.contains('edit')) {
        const row = target.closest('tr');
        document.getElementById('name').value = row.cells[0].textContent;
        document.getElementById('dob').value = row.cells[1].textContent;
        document.getElementById('id_card').value = row.cells[2].textContent;
        document.getElementById('position').value = row.cells[3].textContent;
        document.getElementById('department').value = row.cells[4].textContent;
        
        row.remove();
    }
});


