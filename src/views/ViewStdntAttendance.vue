<template>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 main-content">
          <div class="content-wrapper px-3">
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
                    <span 
                      class="status-badge"
                      :class="getStatusClass(student.status)"
                    >
                      {{ student.status }}
                    </span>
                  </div>
                  <div class="student-details">
                    <small class="text-muted d-block">Date: {{ formatDate(student.date) }}</small>
                    <small class="text-muted d-block">Email: {{ student.email }}</small>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Desktop View -->
            <div class="d-none d-md-block">
              <!-- Controls -->
              <div class="search-controls">
                <button 
                  class="btn btn-outline-primary date-btn d-flex align-items-center gap-2"
                  ref="datePickerBtn"
                >
                  <i class="bi bi-calendar3"></i>
                  <span v-if="selectedDate">{{ formatDate(selectedDate) }}</span>
                  <span v-else>Set Date</span>
                </button>
                <div class="d-flex">
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
  
              <!-- Table -->
              <div class="card shadow-sm table-container">
                <div class="card-header bg-primary text-white">
                  <h4>Student Attendance Records</h4>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Status</th>
                          <th @click="sort('lastName')" class="sortable">
                            Last Name <i :class="getSortIcon('lastName')"></i>
                          </th>
                          <th @click="sort('firstName')" class="sortable">
                            First Name <i :class="getSortIcon('firstName')"></i>
                          </th>
                          <th @click="sort('middleName')" class="sortable">
                            Middle Name <i :class="getSortIcon('middleName')"></i>
                          </th>
                          <th @click="sort('date')" class="sortable">
                            Date <i :class="getSortIcon('date')"></i>
                          </th>
                          <th @click="sort('email')" class="sortable">
                            Email <i :class="getSortIcon('email')"></i>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="student in filteredStudents" :key="student.studentNumber">
                          <td>
                            <span 
                              class="status-badge"
                              :class="getStatusClass(student.status)"
                            >
                              {{ student.status }}
                            </span>
                          </td>
                          <td><strong>{{ student.lastName }}</strong></td>
                          <td>{{ student.firstName }}</td>
                          <td>{{ student.middleName }}</td>
                          <td>{{ formatDate(student.date) }}</td>
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
      </div>
    </div>
  </template>
  
  <script>
  import flatpickr from "flatpickr";
  import "flatpickr/dist/flatpickr.css";
  
  export default {
    name: 'ViewStdntAttendance',
    data() {
      return {
        students: [
          { 
            lastName: 'Doe',
            firstName: 'John',
            middleName: 'Albert',
            studentNumber: 'ST001',
            email: 'john.doe@example.com',
            status: 'PRESENT',
            date: new Date('2024-03-20'),
            selected: false 
          },
          { 
            lastName: 'Doe',
            firstName: 'John',
            middleName: 'Albert',
            studentNumber: 'ST001',
            email: 'john.doe@example.com',
            status: 'LATE',
            date: new Date('2024-03-21'),
            selected: false 
          },
          { 
            lastName: 'Doe',
            firstName: 'John',
            middleName: 'Albert',
            studentNumber: 'ST001',
            email: 'john.doe@example.com',
            status: 'ABSENT',
            date: new Date('2024-03-22'),
            selected: false 
          },
          { 
            lastName: 'Smith',
            firstName: 'Jane',
            middleName: 'Marie',
            studentNumber: 'ST002',
            email: 'jane.smith@example.com',
            status: 'PRESENT',
            date: new Date('2024-03-20'),
            selected: false 
          },
          { 
            lastName: 'Smith',
            firstName: 'Jane',
            middleName: 'Marie',
            studentNumber: 'ST002',
            email: 'jane.smith@example.com',
            status: 'PRESENT',
            date: new Date('2024-03-21'),
            selected: false 
          },
          { 
            lastName: 'Smith',
            firstName: 'Jane',
            middleName: 'Marie',
            studentNumber: 'ST002',
            email: 'jane.smith@example.com',
            status: 'LATE',
            date: new Date('2024-03-22'),
            selected: false 
          },
          { 
            lastName: 'Johnson',
            firstName: 'Mike',
            middleName: 'Cruz',
            studentNumber: 'ST003',
            email: 'mike.johnson@example.com',
            status: 'ABSENT',
            date: new Date('2024-03-20'),
            selected: false 
          },
          { 
            lastName: 'Johnson',
            firstName: 'Mike',
            middleName: 'Cruz',
            studentNumber: 'ST003',
            email: 'mike.johnson@example.com',
            status: 'PRESENT',
            date: new Date('2024-03-21'),
            selected: false 
          },
          { 
            lastName: 'Johnson',
            firstName: 'Mike',
            middleName: 'Cruz',
            studentNumber: 'ST003',
            email: 'mike.johnson@example.com',
            status: 'PRESENT',
            date: new Date('2024-03-22'),
            selected: false 
          },
          { 
            lastName: 'Williams',
            firstName: 'Sarah',
            middleName: 'Elizabeth',
            studentNumber: 'ST004',
            email: 'sarah.williams@example.com',
            status: 'PRESENT',
            date: new Date('2024-03-20'),
            selected: false 
          },
          { 
            lastName: 'Williams',
            firstName: 'Sarah',
            middleName: 'Elizabeth',
            studentNumber: 'ST004',
            email: 'sarah.williams@example.com',
            status: 'ABSENT',
            date: new Date('2024-03-21'),
            selected: false 
          },
          { 
            lastName: 'Williams',
            firstName: 'Sarah',
            middleName: 'Elizabeth',
            studentNumber: 'ST004',
            email: 'sarah.williams@example.com',
            status: 'LATE',
            date: new Date('2024-03-22'),
            selected: false 
          }
        ],
        searchQuery: '',
        sortKey: 'lastName',
        sortOrder: 'asc',
        selectedDate: null,
        datePicker: null,
        mobileDatePicker: null
      }
    },
    mounted() {
      this.initDatePicker();
      this.initDatePickers();
    },
    methods: {
      initDatePicker() {
        this.datePicker = flatpickr(this.$refs.datePickerBtn, {
          enableTime: false,
          dateFormat: "Y-m-d",
          onChange: (selectedDates) => {
            this.selectedDate = selectedDates[0];
          }
        });
      },
      initDatePickers() {
        if (this.$refs.mobileDatePicker) {
          this.mobileDatePicker = flatpickr(this.$refs.mobileDatePicker, {
            enableTime: false,
            dateFormat: "Y-m-d",
            static: true,
            allowInput: false,
            clickOpens: true,
            onChange: (selectedDates) => {
              if (selectedDates.length > 0) {
                this.selectedDate = selectedDates[0];
                this.$forceUpdate();
              }
            }
          });
        }
      },
      sort(key) {
        if (this.sortKey === key) {
          this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortKey = key;
          this.sortOrder = 'asc';
        }
      },
      getSortIcon(column) {
        if (this.sortKey !== column) return 'bi bi-arrow-down-up text-muted';
        return this.sortOrder === 'asc' 
          ? 'bi bi-arrow-down-short' 
          : 'bi bi-arrow-up-short';
      },
      formatDate(date) {
        if (!date) return '';
        const options = { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        };
        return new Date(date).toLocaleDateString('en-US', options);
      },
      getStatusClass(status) {
        return {
          'status-present': status === 'PRESENT',
          'status-absent': status === 'ABSENT',
          'status-late': status === 'LATE'
        }
      }
    },
    computed: {
      filteredStudents() {
        let filtered = this.students;
  
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase();
          filtered = filtered.filter(student => {
            const fullName = `${student.lastName} ${student.firstName} ${student.middleName}`.toLowerCase();
            return fullName.includes(query);
          });
        }
  
        if (this.selectedDate) {
          const selectedDateStr = this.selectedDate.toDateString();
          filtered = filtered.filter(student => 
            new Date(student.date).toDateString() === selectedDateStr
          );
        }
  
        return filtered.sort((a, b) => {
          let aValue = a[this.sortKey];
          let bValue = b[this.sortKey];
  
          if (this.sortKey === 'date') {
            aValue = new Date(aValue);
            bValue = new Date(bValue);
            return this.sortOrder === 'asc' ? aValue - bValue : bValue - aValue;
          }
  
          aValue = String(aValue).toLowerCase();
          bValue = String(bValue).toLowerCase();
          return this.sortOrder === 'asc' ? 
            aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        });
      }
    },
    beforeDestroy() {
      if (this.mobileDatePicker) {
        this.mobileDatePicker.destroy();
      }
    }
  }
  </script>
  
  <style scoped>
  .table-container {
    width: 100%;
    max-width: 1250px;
    margin: 0 auto;
  }
  
  .status-badge {
    padding: 0.4em 0.8em;
    border-radius: 4px;
    font-size: 0.875em;
    font-weight: 500;
    display: inline-block;
  }
  
  .status-present {
    background-color: #d4edda;
    color: #155724;
  }
  
  .status-absent {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  .status-late {
    background-color: #fff3cd;
    color: #856404;
  }
  
  .sortable {
    cursor: pointer;
    user-select: none;
  }
  
  .sortable:hover {
    background-color: rgba(0, 0, 0, 0.05);
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
  
  .main-content {
    margin-left: 250px; 
    margin-top: 90px; 
    padding-top: 20px; 
    width: calc(100% - 250px);
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    overflow-y: auto;
  }
  
  .content-wrapper {
    max-width: 100%;
    padding: 20px 35px; 
    margin-top: 10px; 
  }
  
  .search-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-right: 35px; 
    margin-left: 35px; 
    margin-bottom: 20px;
  }
  
  /* Mobile responsiveness */
  @media (max-width: 768px) {
    .main-content {
      margin-left: 0;
      width: 100%;
      margin-top: 80px;
      padding-top: 15px;
    }
  
    .search-controls {
      margin: 0 15px 15px;
    }
    
    .content-wrapper {
      padding: 15px;
    }
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
  
  .search-btn:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
  }
  
  .search-btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
  
  .form-control.rounded-end-0 {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
  
  .search-btn.rounded-start-0 {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
  
  .search-controls input.form-control,
  .mobile-filters input.form-control {
    border-color: #0d6efd; 
  }
  
  .search-controls input.form-control:focus,
  .mobile-filters input.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
  
  .mobile-date-btn {
    width: 38px;
    height: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .mobile-date-btn + .flatpickr-input {
    display: none !important;
  }
  
  .flatpickr-mobile {
    display: none !important;
  }
  
  @media screen and (max-width: 768px) {
    .flatpickr-calendar {
      width: 280px !important;
      font-size: 14px !important;
    }
  
    .flatpickr-months .flatpickr-month {
      height: 40px !important;
    }
  
    .flatpickr-current-month {
      font-size: 15px !important;
      padding: 5px 0 !important;
    }
  
    .flatpickr-monthDropdown-months {
      height: 28px !important;
    }
  
    .flatpickr-days {
      width: 280px !important;
    }
  
    .dayContainer {
      width: 280px !important;
      min-width: 280px !important;
      max-width: 280px !important;
    }
  
    .flatpickr-day {
      height: 35px !important;
      line-height: 35px !important;
      font-size: 13px !important;
    }
  }
  
  .mobile-date-btn + .flatpickr-input,
  .flatpickr-mobile,
  .mobile-date-btn > span {
    display: none !important;
  }
  
  .mobile-date-btn {
    width: 38px;
    height: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .mobile-student-cards {
    height: calc(100vh - 180px); 
    overflow-y: auto;
    padding: 10px 15px;
    margin-top: 60px; 
    -webkit-overflow-scrolling: touch;
  }
  
  .student-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 15px;
    margin-bottom: 10px;
  }
  
  @media (max-width: 768px) {
    .content-wrapper {
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
  }
  </style>