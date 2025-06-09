document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const tableBody = document.querySelector('#violations-table tbody');
  const violationForm = document.getElementById('addViolationForm');
  const editViolationForm = document.getElementById('editViolationForm');
  const confirmResolveBtn = document.getElementById('confirmResolveBtn');
  const resolveModal = document.getElementById('resolveModal');
  const searchInput = document.getElementById('searchInput'); // Optional search input

  let violations = [];
  let violationToEditId = null;
  let violationToResolveId = null;

  // Render violations into table
  function renderViolations() {
    if (!violations.length) {
      tableBody.innerHTML = `<tr><td colspan="9" style="text-align:center;">No violations found.</td></tr>`;
      return;
    }

    tableBody.innerHTML = violations.map(v => `
      <tr>
        <td>${v.student_id || ''}</td>
        <td>${v.first_name || ''}</td>
        <td>${v.last_name || ''}</td>
        <td>${v.mi || ''}</td>
        <td>${v.course || ''}</td>
        <td>${v.section || ''}</td>
        <td>${v.violation || ''}</td>
        <td>${new Date(v.date_created).toLocaleDateString() || ''}</td>
        <td>
          <button class="edit-btn" data-id="${v.id}">Edit</button>
          <button class="resolve-btn" data-id="${v.id}">Resolve</button>
        </td>
      </tr>
    `).join('');
  }

  // Fetch violations from API, optional search query
  async function fetchViolations(query = '') {
    try {
      const baseUrl = 'http://172.16.0.28:8000/api_violations.php';
      const url = query ? `${baseUrl}?q=${encodeURIComponent(query)}` : baseUrl;
      const res = await fetch(url);
      if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
      violations = await res.json();
      renderViolations();
    } catch (e) {
      console.error('Error fetching violations:', e);
      tableBody.innerHTML = `<tr><td colspan="9">Failed to load data.</td></tr>`;
    }
  }

  // Close Add Violation sidebar (implement as needed)
  function closeFormSidebar() {
    // Example: document.getElementById('addSidebar').style.display = 'none';
  }

  // Close Edit Violation sidebar (implement as needed)
  function closeEditSidebar() {
    // Example: document.getElementById('editSidebar').style.display = 'none';
  }

  // Open Edit Violation sidebar and populate form
  function openEditViolation(id) {
    const violation = violations.find(v => v.id == id);
    if (!violation) return alert('Violation not found.');

    violationToEditId = id;
    // Populate edit form fields
    editViolationForm.student_id.value = violation.student_id || '';
    editViolationForm.first_name.value = violation.first_name || '';
    editViolationForm.last_name.value = violation.last_name || '';
    editViolationForm.mi.value = violation.mi || '';
    editViolationForm.course.value = violation.course || '';
    editViolationForm.section.value = violation.section || '';
    editViolationForm.violation.value = violation.violation || '';
    editViolationForm.todo.value = violation.todo || '';

    // Show edit sidebar (implement as needed)
    // document.getElementById('editSidebar').style.display = 'block';
  }

  // Open resolve confirmation modal
  function openResolveModal(id) {
    violationToResolveId = id;
    // Show modal (implement as needed)
    resolveModal.style.display = 'block';
  }

  // Add Violation form submit handler (POST)
  violationForm.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(violationForm);
    const data = Object.fromEntries(formData.entries());

    try {
      const res = await fetch('http://172.16.0.28:8000/api_violations.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await res.json();
      if (res.ok && result.status === 'success') {
        alert('Violation added successfully.');
        closeFormSidebar();
        fetchViolations();
      } else {
        alert('Error adding violation: ' + (result.message || 'Unknown error'));
      }
    } catch (error) {
      alert('Failed to add violation.');
      console.error(error);
    }
  });

  // Edit Violation form submit handler (PUT)
  editViolationForm.addEventListener('submit', async e => {
    e.preventDefault();
    if (!violationToEditId) {
      alert('No violation selected to update.');
      return;
    }

    const formData = new FormData(editViolationForm);
    const data = Object.fromEntries(formData.entries());

    try {
      const res = await fetch(`http://172.16.0.28:8000/api_violations.php?id=${violationToEditId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await res.json();
      if (res.ok && result.status === 'success') {
        alert('Violation updated successfully.');
        closeEditSidebar();
        fetchViolations();
      } else {
        alert('Error updating violation: ' + (result.message || 'Unknown error'));
      }
    } catch (error) {
      alert('Failed to update violation.');
      console.error(error);
    }
  });

  // Delegate clicks on Edit and Resolve buttons in table
  tableBody.addEventListener('click', e => {
    if (e.target.classList.contains('edit-btn')) {
      const id = e.target.dataset.id;
      openEditViolation(id);
    } else if (e.target.classList.contains('resolve-btn')) {
      const id = e.target.dataset.id;
      openResolveModal(id);
    }
  });

  // Resolve modal confirm handler (DELETE)
  confirmResolveBtn.addEventListener('click', async () => {
    if (!violationToResolveId) return;
    try {
      const res = await fetch(`http://172.16.0.28:8000/api_violations.php?id=${violationToResolveId}`, {
        method: 'DELETE'
      });
      const result = await res.json();
      if (res.ok && result.status === 'success') {
        alert('Violation marked as resolved.');
        resolveModal.style.display = 'none';
        violationToResolveId = null;
        fetchViolations();
      } else {
        alert('Failed to resolve violation: ' + (result.message || 'Unknown error'));
      }
    } catch (error) {
      alert('Failed to resolve violation.');
      console.error(error);
    }
  });

  // Optional: search input handler (debounced)
  if (searchInput) {
    let searchTimeout = null;
    searchInput.addEventListener('input', e => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        fetchViolations(searchInput.value.trim());
      }, 300);
    });
  }

  // Initial fetch to load data
  fetchViolations();
});
