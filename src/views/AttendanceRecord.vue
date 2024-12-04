<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 offset-md-3" style="margin-top:100px; padding-right: 30px;">
        <!-- Mobile View -->
        <div class="d-block d-md-none mb-3">
          <div class="mobile-filters">
            <div class="d-flex gap-2 mb-3">
              <button 
                class="btn btn-outline-primary mobile-date-btn"
                ref="mobileDatePicker"
              >
                <i class="bi bi-calendar3"></i>
              </button>
              <div class="d-flex flex-grow-2">
                <input 
                  type="text" 
                  v-model="searchQuery" 
                  placeholder="Enter Student Name" 
                  class="form-control rounded-end-0"
                />
                <button class="btn btn-outline-primary search-btn rounded-start-0">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="mobile-student-cards">
            <div v-for="student in filteredStudents" 
                 :key="student.studentNumber" 
                 class="student-card mb-3 p-3"
            >
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">{{ student.lastName }}, {{ student.firstName }}</h6>
                <select 
                  v-model="student.status" 
                  class="form-select form-select-sm attendance-select"
                  :class="getStatusClass(student.status)"
                  @change="handleStatusChange(student)"
                >
                  <option value="PRESENT">PRESENT</option>
                  <option value="ABSENT">ABSENT</option>
                  <option value="LATE">LATE</option>
                </select>
              </div>
              <div class="student-details">
                <small class="text-muted d-block">ID: {{ student.studentNumber }}</small>
                <small class="text-muted d-block">Email: {{ student.email }}</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="d-none d-md-block">
          <!-- Date Selector -->
          <div class="date-section mb-4" style="margin-left: -35px;">
            <div class="d-flex align-items-center">
              <button 
                class="btn btn-outline-primary date-btn d-flex align-items-center gap-2"
                ref="datePickerBtn"
              >
                <i class="bi bi-calendar3"></i>
                <span v-if="selectedDate">{{ formatDate(selectedDate) }}</span>
                <span v-else>Set Date</span>
              </button>
            </div>
          </div>

          <div class="card shadow-sm table-container" style="margin-left: -35px;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
              <div class="header-text me-2">
                <h4>Take Attendance</h4>
              </div>
              <div>
                <input type="text" v-model="searchQuery" placeholder="Search" class="form-control form-control-sm d-inline-block w-auto me-2" />
                <button class="btn btn-light btn-sm me-2" @click="deleteSelectedStudents">
                  <span class="material-icons me-2">delete</span>Delete
                </button>
                <button class="btn btn-light btn-sm" @click="showAddStudentDialog = true">
                  <span class="material-symbols-outlined">add</span>Add Student
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 student-table">
                  <thead class="table-light">
                    <tr>
                      <th>Status</th>
                      <th @click="sort('lastName')" class="sortable">
                        Last Name
                        <i :class="getSortIcon('lastName')"></i>
                      </th>
                      <th @click="sort('firstName')" class="sortable">
                        First Name
                        <i :class="getSortIcon('firstName')"></i>
                      </th>
                      <th @click="sort('middleName')" class="sortable">
                        Middle Name
                        <i :class="getSortIcon('middleName')"></i>
                      </th>
                      <th @click="sort('email')" class="sortable">
                        Email
                        <i :class="getSortIcon('email')"></i>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="student in filteredStudents" :key="student.studentNumber">
                      <td>
                        <select 
                          v-model="student.status" 
                          class="form-select form-select-sm attendance-select"
                          :class="{
                            'status-present': student.status === 'PRESENT',
                            'status-absent': student.status === 'ABSENT',
                            'status-late': student.status === 'LATE'
                          }"
                        >
                          <option value="PRESENT">PRESENT</option>
                          <option value="ABSENT">ABSENT</option>
                          <option value="LATE">LATE</option>
                        </select>
                      </td>
                      <td><strong>{{ student.lastName }}</strong></td>
                      <td>{{ student.firstName }}</td>
                      <td>{{ student.middleName }}</td>
                      <td>{{ student.email }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Student Dialog -->
    <div v-if="showAddStudentDialog" class="modal-overlay">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add an Existing Student</h5>
            <button type="button" class="btn-close" @click="closeAddStudentDialog"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addStudent">
              <div class="mb-3">
                <label for="studentName" class="form-label">Student Name</label>
                <select 
                  id="studentName" 
                  v-model="selectedStudent" 
                  class="form-select" 
                  required
                >
                  <option value="">Select a student</option>
                  <option 
                    v-for="student in availableStudents" 
                    :key="student.studentNumber" 
                    :value="student"
                  >
                    {{ student.lastName }}, {{ student.firstName }} {{ student.middleName }}
                  </option>
                </select>
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" @click="closeAddStudentDialog">Close</button>
                <button type="submit" class="btn btn-primary" :disabled="!selectedStudent">Add Student</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Students Dialog -->
    <div v-if="showDeleteDialog" class="modal-overlay">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Students</h5>
            <button type="button" class="btn-close" @click="cancelDelete"></button>
          </div>
          <div class="modal-body">
            <div class="student-list">
              <div v-for="student in students" :key="student.studentNumber" class="student-item">
                <div class="form-check">
                  <input 
                    type="checkbox" 
                    class="form-check-input" 
                    v-model="student.selected"
                    :id="student.studentNumber"
                  />
                  <label class="form-check-label" :for="student.studentNumber">
                    {{ student.lastName }}, {{ student.firstName }}
                  </label>
                </div>
              </div>
            </div>
            <div class="mt-3 d-flex justify-content-end gap-2">
              <button class="btn btn-secondary" @click="cancelDelete">Cancel</button>
              <button class="btn btn-danger" @click="confirmDelete">Delete Selected</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

export default {
  data() {
    return {
      students: [],
      allStudents: [],
      availableStudents: [],
      selectedStudent: null,
      selectedDate: null,
      searchQuery: '',
      showAddStudentDialog: false,
      showDeleteDialog: false,
      sortKey: '',
      sortOrder: 'asc',
      isMobile: false
    };
  },
  computed: {
    filteredStudents() {
      let filtered = this.students.filter(student => {
        const searchString = Object.values(student).join(' ').toLowerCase();
        return searchString.includes(this.searchQuery.toLowerCase());
      });

      // Apply sorting
      return filtered.sort((a, b) => {
        const sortValueA = a[this.sortKey] || '';
        const sortValueB = b[this.sortKey] || '';
        if (this.sortOrder === 'asc') {
          return sortValueA.localeCompare(sortValueB);
        } else {
          return sortValueB.localeCompare(sortValueA);
        }
      });
    }
  },
  methods: {
    sort(key) {
      if (this.sortKey === key) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortOrder = 'asc';
      }
    },
    getSortIcon(key) {
      return this.sortKey === key ? (this.sortOrder === 'asc' ? 'bi bi-caret-down-fill' : 'bi bi-caret-up-fill') : '';
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    getStatusClass(status) {
      switch (status) {
        case 'PRESENT': return 'status-present';
        case 'ABSENT': return 'status-absent';
        case 'LATE': return 'status-late';
        default: return '';
      }
    },
    handleStatusChange(student) {
      // Additional handling can be done when attendance status is changed
    },
    addStudent() {
      if (this.selectedStudent && !this.students.some(student => student.studentNumber === this.selectedStudent.studentNumber)) {
        this.students.push(this.selectedStudent);
        this.selectedStudent = null;
        this.closeAddStudentDialog();
      }
    },
    closeAddStudentDialog() {
      this.showAddStudentDialog = false;
    },
    deleteSelectedStudents() {
      this.showDeleteDialog = true;
    },
    confirmDelete() {
      this.students = this.students.filter(student => !student.selected);
      this.showDeleteDialog = false;
    },
    cancelDelete() {
      this.showDeleteDialog = false;
    }
  },
  mounted() {
    // Initialize date picker
    flatpickr(this.$refs.mobileDatePicker, { dateFormat: "Y-m-d", onChange: this.handleDateChange });
    flatpickr(this.$refs.datePickerBtn, { dateFormat: "Y-m-d", onChange: this.handleDateChange });
  }
};
</script>

<style scoped>
/* Add your custom styles here */
</style>

<style scoped>
.table-container {
  width: 100%;
  max-width: 1250px;
  margin: 0 auto;
}

.student-table {
  width: 100%;
  height: 100%;
  table-layout: fixed;
}

.table-responsive {
  max-height: 1000px;
  overflow-y: auto;
}

@media (max-width: 768px) {
  .table-container {
    margin-left: 0 !important;
    padding: 0 15px;
  }
}

@media (max-width: 768px) {
  .table-container {
    width: 100%;
    padding: 0 15px;
    margin-top: 50px;
    margin-bottom: 30px;
  }
  
  .student-table th,
  .student-table td {
    font-size: 12px;
    padding: 6px;
  }
}

.header-text {
  flex: 1;
  text-align: left;
}

.student-table th, 
.student-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

.student-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.student-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

select {
  width: 100%;
  padding: 5px;
  border: 1px solid #ddd;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-dialog {
  background: white;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  width: 500px;
  height: 500px;
}

.modal-header,
.modal-body {
  padding: 15px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #dee2e6;
}

.modal-title {
  margin: 0;
}

.btn-close {
  border: none;
  background: none;
  font-size: 1.25rem;
  line-height: 1;
}

.btn-close:focus {
  outline: none;
  box-shadow: none;
}

.modal-body {
  display: flex;  
  flex-direction: column;
}

.sortable {
  cursor: pointer;
  user-select: none;
  position: relative;
}

.sortable:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.sortable i {
  margin-left: 5px;
  font-size: 0.8em;
  vertical-align: middle;
}

.table th {
  white-space: nowrap;
}

.date-section {
  padding: 0 15px;
}

.date-btn {
  padding: 8px 16px;
  font-size: 1rem;
  background: white;
  border-radius: 6px;
  transition: all 0.2s;
}

.date-btn:hover {
  background: #f8f9fa;
}

.attendance-select {
  width: 120px;
  height: 38px;
  padding: 6px 30px 6px 12px;
  font-size: 14px;
  line-height: 1.5;
  border-radius: 4px;
  border: 1px solid #dee2e6;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  cursor: pointer;
  background-color: #fff;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 16px;
}

.attendance-select::-ms-expand {
  display: none;
}

/* Keep existing status colors but add background-image to maintain dropdown arrow */
.status-present {
  background-color: #d4edda !important;
  color: #155724 !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23155724' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 16px;
}

.status-absent {
  background-color: #f8d7da !important;
  color: #721c24 !important;  
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23721c24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 16px;
}

.status-late {
  background-color: #fff3cd !important;
  color: #856404 !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23856404' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 16px;
}

/* Mobile Styles */
.mobile-student-cards {
  max-height: calc(100vh - 200px);
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.student-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.student-card:active {
  transform: scale(0.98);
}

.attendance-select {
  width: 110px;
  height: 35px;
  border-radius: 4px;
  font-size: 14px;
  padding: 5px;
  background-image: url("data:image/svg+xml,...");
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 12px;
}

/* Status colors */
.status-present {
  background-color: #d4edda !important;
  color: #155724 !important;
  border-color: #c3e6cb !important;
}

.status-absent {
  background-color: #f8d7da !important;
  color: #721c24 !important;
  border-color: #f5c6cb !important;
}

.status-late {
  background-color: #fff3cd !important;
  color: #856404 !important;
  border-color: #ffeeba !important;
}

/* Media Queries */
@media (max-width: 768px) {
  .container-fluid {
    padding: 10px;
  }
  
  .col-12 {
    margin-top: 70px !important;
    padding-right: 15px !important;
  }

  .mobile-filters {
    position: sticky;
    top: 90px; /* Increased from 70px */
    left: 0;
    right: 0;
    background: white;
    padding: 10px 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 15px;
  }

  .student-details {
    font-size: 12px;
    line-height: 1.4;
  }
}

/* Touch-friendly inputs */
@media (hover: none) {
  .attendance-select {
    padding: 8px 24px 8px 8px;
  }

  input[type="text"],
  select {
    font-size: 16px !important; /* Prevents iOS zoom on focus */
  }
}

/* iPad & Tablets */
@media (min-width: 768px) and (max-width: 1024px) {
  .table-container {
    margin-left: 0 !important;
  }
}

.student-list {
  max-height: 300px;
  overflow-y: auto;
}

.student-item {
  padding: 8px;
  border-bottom: 1px solid #dee2e6;
}

.student-item:last-child {
  border-bottom: none;
}

.form-check-input:checked {
  background-color: #dc3545;
  border-color: #dc3545;
}

/* Mobile styles */
.mobile-filters {
position: fixed;
top: 90px; /* Increased from 70px */
left: 0;
right: 0;
z-index: 998;
background: white;
padding: 10px 15px;
box-shadow: 0 2px 4px rgba(0,0,0,0.1);
margin-top: 20px;
}

.mobile-student-cards {
padding: 10px 15px;
margin-top: 10px;
height: calc(100vh - 200px);
overflow-y: auto;
-webkit-overflow-scrolling: touch;
}

.student-card {
background: white;
border-radius: 8px;
box-shadow: 0 2px 4px rgba(0,0,0,0.1);
padding: 15px;
margin-bottom: 10px;
}

.mobile-date-btn {
width: 38px;
height: 38px;
padding: 0;
display: flex;
align-items: center;
justify-content: center;
}

.search-btn {
width: 38px;
height: 38px;
padding: 0;
display: flex;
align-items: center;
justify-content: center;
border-color: #0d6efd;
border-left: none;
}

@media (max-width: 768px) {
.content-wrapper {
  padding-top: 20px;
  height: 100vh;
  overflow: hidden;
}

.mobile-student-cards::-webkit-scrollbar {
  width: 4px;
}

.mobile-student-cards::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.2);
  border-radius: 4px;
}

.form-select-sm {
  height: 32px;
  padding: 4px 24px 4px 8px;
  font-size: 13px;
}

.flatpickr-calendar {
  width: 260px !important;
  font-size: 13px !important;
}

.flatpickr-day {
  height: 32px !important;
  line-height: 32px !important;
  font-size: 12px !important;
}

.content-wrapper {
  padding-top: 80px;
}

.mobile-filters .form-control {
  height: 36px;
  font-size: 14px;
}

.mobile-date-btn,
.search-btn {
  width: 36px;
  height: 36px;
}
}

/* Main layout */
.main-content {
padding-top: 80px; /* Increased padding to account for header */
}

/* Mobile specific styles */
@media (max-width: 768px) {
.content-wrapper {
  padding-top: 0;
}

.mobile-filters {
  position: sticky;
  top: 80px; /* Match main content padding */
  background: white;
  padding: 10px 15px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-top: 20px;
}

.mobile-student-cards {
  margin-top: 20px;
  padding: 10px 15px;
  height: calc(100vh - 220px); /* Adjusted for new spacing */
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.student-card {
  margin-bottom: 10px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 15px;
}
}

/* Mobile specific styles */
@media (max-width: 768px) {
.main-content {
  padding-top: 80px;
}

.content-wrapper {
  padding: 15px;
}

.mobile-filters {
  position: relative;
  background: white;
  padding: 10px 15px;
  margin-bottom: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-student-cards {
  padding: 0;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.student-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 15px;
  margin-bottom: 10px;
}
}

@media (max-width: 768px) {
.main-content {
  padding-top: 50px; /* Reduced from 60px */
}

.content-wrapper {
  padding: 10px 15px;
}

.mobile-filters {
  position: relative;
  background: white;
  padding: 8px 10px;
  margin-bottom: 10px; /* Reduced from 15px */
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-student-cards {
  padding: 0;
  margin-top: 5px; /* Reduced spacing */
  height: calc(100vh - 180px); /* Adjusted calculation */
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.student-card:first-child {
  margin-top: 0; /* Remove top margin from first card */
}

.student-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 15px;
  margin-bottom: 10px;
}
}

@media (max-width: 768px) {
.main-content {
  padding-top: 40px; /* Further reduced */
}

.content-wrapper {
  padding: 5px 15px;
}

.mobile-filters {
  position: relative;
  background: white;
  padding: 8px 10px;
  margin-bottom: 8px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-top: -5px; /* Pull up slightly */
}

.mobile-student-cards {
  padding: 0;
  margin-top: 0;
  height: calc(100vh - 160px); /* Adjusted for new spacing */
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.student-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 15px;
  margin-bottom: 10px;
}

.student-card:first-child {
  margin-top: 5px;
}
}

@media (max-width: 768px) {
.main-content {
  padding-top: 20px; /* Significantly reduced */
}

.content-wrapper {
  padding: 5px 15px;
}

.mobile-filters {
  position: relative;
  background: white;
  padding: 8px 10px;
  margin-top: -15px; /* Pull filters up */
  margin-bottom: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-student-cards {
  padding: 0;
  margin-top: 10px;
  height: calc(100vh - 140px); /* Adjusted for new spacing */
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.student-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 15px;
  margin-bottom: 10px;
}
}

@media (max-width: 768px) {
.main-content {
  padding-top: 10px; 
}



.mobile-filters {
  position: relative;
  background: white;
  padding: 8px 10px;
  margin-top: -30px; 
  margin-bottom: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-student-cards {
  padding: 0;
  margin-top: 100px; /* Increased margin to push cards down */
  height: calc(100vh - 140px); /* Adjusted for new spacing */
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}


}
</style>