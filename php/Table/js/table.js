 let tables = [];
        let tableToDelete = null;
        document.addEventListener('DOMContentLoaded', fetchTables);

        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const filteredTables = searchTerm
                ? tables.filter(table => table.table_number.toString().includes(searchTerm))
                : tables;
            renderTables(filteredTables);
        });

        document.getElementById('tableForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const tableId = formData.get('table_id');

            const url = tableId ? 'update_table.php' : 'add_table.php';

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchTables();
                        closeModal();
                        showToast(tableId ? 'Table updated successfully!' : 'Table added successfully!');
                    } else {
                        showToast('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('An error occurred. Please try again.', 'error');
                    console.error('Error:', error);
                });
        });

        function fetchTables() {
            fetch('get_tables.php')
                .then(response => response.json())
                .then(data => {
                    tables = data.tables;
                    updateStatusCounts(data.tables);
                    renderTables(data.tables);
                })
                .catch(error => {
                    console.error('Error fetching tables:', error);
                    showToast('Failed to load tables', 'error');
                });
        }

        function updateStatusCounts(tables) {
            let available = 0;
            let reserved = 0;
            let occupied = 0;

            tables.forEach(table => {
                if (table.status == 0) available++;
                else if (table.status == 2) reserved++;
                else if (table.status == 1) occupied++;
            });

            document.getElementById('available-count').textContent = available;
            document.getElementById('reserved-count').textContent = reserved;
            document.getElementById('occupied-count').textContent = occupied;
        }

        function renderTables(tables) {
            const grid = document.getElementById('tablesGrid');
            grid.innerHTML = '';

            tables.forEach(table => {
                const card = document.createElement('div');

                let statusClass = '';
                let statusText = '';
                let cardClass = '';

                if (table.status == 0) {
                    statusClass = 'status-available';
                    statusText = 'AVAILABLE';
                    cardClass = 'available';
                } else if (table.status == 2) {
                    statusClass = 'status-reserved';
                    statusText = 'RESERVED';
                    cardClass = 'reserved';
                } else {
                    statusClass = 'status-occupied';
                    statusText = 'OCCUPIED';
                    cardClass = 'occupied';
                }

                card.className = `table-card ${cardClass}`;

                card.innerHTML = `
                    <div class="table-status ${statusClass}">${statusText}</div>
                    <div class="table-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="table-info">
                        <div class="table-name">Table ${table.table_number}</div>
                        <div class="table-seats">${table.seats} seats</div>
                    </div>
                    <div class="table-actions">
                        <button class="action-btn" onclick="editTable(${table.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn" onclick="deleteTable(${table.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Table';
            document.getElementById('tableForm').reset();
            document.getElementById('tableId').value = '';
            document.getElementById('tableModal').style.display = 'flex';
        }

        function editTable(id) {
            const table = tables.find(t => t.id == id);
            if (table) {
                document.getElementById('modalTitle').textContent = 'Edit Table';
                document.getElementById('tableId').value = table.id;
                document.getElementById('tableNumber').value = table.table_number;
                document.getElementById('tableSeats').value = table.seats;
                document.getElementById('tableStatus').value = table.status;
                document.getElementById('tablePrice').value = table.price;
                document.getElementById('tableModal').style.display = 'flex';
            }
        }

        function deleteTable(id) {
            tableToDelete = id;
            document.getElementById('deleteTableId').value = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function confirmDelete() {
            const id = document.getElementById('deleteTableId').value;

            fetch('delete_table.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `table_id=${id}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchTables();
                        closeDeleteModal();
                        showToast('Table deleted successfully!');
                    } else {
                        showToast('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('An error occurred. Please try again.', 'error');
                    console.error('Error:', error);
                });
        }

        function closeModal() {
            document.getElementById('tableModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('successToast');
            toast.textContent = message;
            toast.style.display = 'block';

            if (type === 'error') {
                toast.style.backgroundColor = '#f44336';
            } else {
                toast.style.backgroundColor = '#4caf50';
            }

            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        setInterval(fetchTables, 30000);

        