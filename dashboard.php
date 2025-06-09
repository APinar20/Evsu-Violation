<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Violations Dashboard</title>
   <link rel="stylesheet" href="dashboard.css" />
  
</head>
<body>
  <header>
    <h1>Student Violations Dashboard</h1>
    <nav>
      <button id="addViolationBtn">Add Violation</button>
      <button onclick="window.location.href='notifications.php'">Notifications</button>
    </nav>
  </header>

  <div class="container">
    <div class="search-section">
      <input id="searchInput" placeholder="Search Student ID or Last Name" />
      <button id="searchBtn">Search</button>
    </div>

    <table class="violations-table">
      <thead>
        <tr>
          <th>Student ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Middle Initial</th>
          <th>Program</th>
          <th>Section</th>
          <th>Violation</th>
          <th>To Do</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody id="violationsTableBody">
        <?php
        $sql = "SELECT * FROM student_violations";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            echo "<tr>
              <td>{$row['student_id']}</td>
              <td>{$row['first_name']}</td>
              <td>{$row['last_name']}</td>
              <td>{$row['mi']}</td>
              <td>{$row['course']}</td>
              <td>{$row['section']}</td>
              <td>{$row['violation']}</td>
              <td>{$row['todo']}</td>
              <td>{$row['created_at']}</td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='9'>No violations found.</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>

  <div class="sidebar-form" id="formSidebar">
    <form id="violationForm">
      <input name="student_id" placeholder="Student ID" required />
      <input name="fname" placeholder="First Name" required />
      <input name="lname" placeholder="Last Name" required />
      <input name="mname" placeholder="Middle Initial" />

      <input name="program" placeholder="Program" required />
      <input name="section" placeholder="Section" required />

      <select name="violation" required>
        <option value="">-- Select Violation --</option>
        <option value="Noise in the library">Noise in the library</option>
        <option value="Unauthorized use of computer">Unauthorized use of computer</option>
        <option value="Eating or drinking">Eating or drinking</option>
        <option value="Using cellphone">Using cellphone</option>
        <option value="Improper use of facilities">Improper use of facilities</option>
      </select>

      <select name="todo" required>
        <option value="">-- Select To Do --</option>
        <option value="Verbal warning">Verbal warning</option>
        <option value="Written explanation">Written explanation</option>
        <option value="Report to guidance">Report to guidance</option>
        <option value="Community service">Community service</option>
      </select>

      <div class="form-buttons">
        <button type="submit">Save</button>
        <button type="button" id="cancelFormBtn">Cancel</button>
      </div>
    </form>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const addBtn = document.getElementById('addViolationBtn');
  const formSidebar = document.getElementById('formSidebar');
  const violationForm = document.getElementById('violationForm');
  const cancelFormBtn = document.getElementById('cancelFormBtn');
  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');

  function openFormSidebar() {
    formSidebar.classList.add('show');
  }

  function closeFormSidebar() {
    formSidebar.classList.remove('show');
    violationForm.reset();
  }

  addBtn.addEventListener('click', openFormSidebar);
  cancelFormBtn.addEventListener('click', closeFormSidebar);

  violationForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(violationForm);
    const data = Object.fromEntries(formData.entries());

    try {
      const res = await fetch('save_violation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await res.json();
      if (res.ok && result.status === 'success') {
        alert('Violation added successfully.');
        closeFormSidebar();
        window.location.reload(); // Reload to see the new data
      } else {
        alert('Error: ' + (result.message || 'Unknown error'));
      }
    } catch (err) {
      alert('Failed to add violation.');
      console.error(err);
    }
  });

  searchBtn.addEventListener('click', () => {
    const query = searchInput.value.trim();
    if (query) {
      window.location.href = `?q=${encodeURIComponent(query)}`;
    }
  });
});
</script>
</body>
</html>
