<template>
  <div class="header-container">
    <!-- Main Card Header -->
    <div class="card header-card">
      <div class="card-body d-flex align-items-center justify-content-between">
        <!-- Dashboard Title -->
        <h3 class="card-title mb-0 d-none d-sm-block"><b>{{ cardTitle }}</b></h3>

        <!-- DateTime Display -->
        <div class="datetime-display">
          {{ currentDateTime }}
        </div>

        <!-- Profile Section -->
        <div class="profile-section d-flex align-items-center">
          <div class="dropdown me-3 position-relative" ref="dropdown">
            <button
              class="btn btn-light p-0 position-relative"
              type="button"
              @click="toggleDropdown"
              aria-expanded="false"
              style="border: none; background: transparent;"
            >
              <span class="material-icons">mail</span>
            </button>
            <ul class="dropdown-menu" ref="dropdownMenu">
              <li><a class="dropdown-item" href="#">No new notifications</a></li>
              <li><a class="dropdown-item" href="#">Another notification</a></li>
            </ul>
          </div>

          <div class="user-info d-flex align-items-center">
            <i class="material-icons">account_circle</i>
            <div class="account-info d-flex flex-column justify-content-center bg-light px-3 shadow-account"
                 style="height: 50px; width: 270px; border-radius: 20px;">
              <span class="fw-bold">{{ userInfo.lastName }}, {{ userInfo.firstName }}</span> 
              <span>{{ userInfo.email }}</span> 
            </div>

            <div class="dropdown ms-3">
              <button
                class="btn btn-light p-0"
                type="button"
                @click="toggleUserDropdown"
                aria-expanded="false"
                style="border: none; background: transparent;"
              >
                <span class="material-icons">arrow_drop_down</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right" ref="userDropdownMenu">
                <li><a class="dropdown-item" href="#" @click.prevent="openEditProfileDialog">Edit Profile</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Profile Dialog -->
    <div class="dialog-overlay" v-if="isEditProfileDialogVisible">
      <div class="dialog-content">
        <!-- Edit Profile Header -->
        <h2>Edit Profile</h2>

        <!-- Profile Picture Section Below the Header -->
        <div class="profile-picture-container text-center">
          <div class="profile-picture">
            <input
              type="file"
              accept="image/*"
              @change="handleFileUpload"
              class="profile-picture-input"
            />
            <img
              :src="profilePictureUrl || userInfo.profilePictureUrl"
              alt="Profile Picture"
              class="profile-picture-img"
            />
          </div>
          <button class="btn btn-link mt-2" @click="handleFileUploadClick">Upload Picture</button>
        </div>

        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="firstName">First Name:</label>
            <div class="d-flex align-items-center">
              <span>{{ form.firstName }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="lastName">Last Name:</label>
            <div class="d-flex align-items-center">
              <span>{{ form.lastName }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="middleName">Middle Name:</label>
            <div class="d-flex align-items-center">
              <span>{{ form.middleName }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="gender">Gender:</label>
            <div class="d-flex align-items-center">
              <select
                v-if="editingField === 'gender'"
                id="gender"
                v-model="form.gender"
                class="form-control"
              >
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
              <span v-else>{{ form.gender }}</span>
              <button
                type="button"
                class="btn btn-link ms-2"
                @click="editingField = 'gender'"
              >
                Edit
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="department">College Department:</label>
            <div class="d-flex align-items-center">
              <select
                v-if="editingField === 'department'"
                id="department"
                v-model="form.department"
                class="form-control"
              >
                <option value="Computer Studies">Computer Studies</option>
                <option value="College Of Business and Accountancy">College Of Business and Accountancy</option>
                <option value="College of Education, Arts, and Science">College of Education, Arts, and Science</option>
                <option value="College of Hospitality and Tourism Management">College of Hospitality and Tourism Management</option>
              </select>
              <span v-else>{{ form.department }}</span>
              <button
                type="button"
                class="btn btn-link ms-2"
                @click="editingField = 'department'"
              >
                Edit
              </button>
            </div>
          </div>

          <div class="form-actions mt-3">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" @click="closeDialog">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminHeader',
  data() {
    return {
      currentDateTime: '',
      timer: null,
      isEditProfileDialogVisible: false,
      editingField: null,
      userInfo: {
        firstName: '',
        lastName: '',
        email: '',
        profilePictureUrl: '', 
      },
      profilePictureUrl: '', 
      form: {
        firstName: '',
        lastName: '',
        middleName: '',
        gender: '',
        department: '',
      },
    };
  },
  computed: {
    cardTitle() {
      switch (this.$route.path) {
        case '/clearance':
          return 'Clearance';
        case '/ClearanceRecord':
          return 'Clearance';
        case '/attendance':
          return 'Attendance';
        case '/home':
          return 'Dashboard';
        case '/class':
          return 'Create & View Class';
        case '/ViewStudentAttendance':
          return 'View Student Attendance';
        default:
          return 'Dashboard';
      }
    },
  },
  methods: {
    toggleDropdown() {
      const dropdownElement = this.$refs.dropdown;
      const dropdownMenu = this.$refs.dropdownMenu;
      dropdownMenu.classList.toggle('show');
      dropdownElement.classList.toggle('show');
      dropdownElement.setAttribute('aria-expanded', dropdownMenu.classList.contains('show'));
    },
    toggleUserDropdown() {
      const userDropdownMenu = this.$refs.userDropdownMenu;
      userDropdownMenu.classList.toggle('show');
    },
    openEditProfileDialog() {
      this.isEditProfileDialogVisible = true;
      this.form = { ...this.userInfo };
    },
    closeDialog() {
      this.isEditProfileDialogVisible = false;
      this.editingField = null;
    },
    submitForm() {
      this.userInfo = { ...this.form, profilePictureUrl: this.profilePictureUrl };
      this.closeDialog();
      this.saveUserInfo();
    },
    updateDateTime() {
      const now = new Date();
      if (window.innerWidth <= 768) {
        const date = now.toLocaleDateString('en-US', {
          month: '2-digit',
          day: '2-digit',
          year: 'numeric',
        });
        const time = now.toLocaleTimeString('en-US', {
          hour: '2-digit',
          minute: '2-digit',
          hour12: true,
        });
        this.currentDateTime = `${date} - ${time}`;
      } else {
        const options = {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        };
        this.currentDateTime = now.toLocaleDateString('en-US', options);
      }
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          this.profilePictureUrl = reader.result;
        };
        reader.readAsDataURL(file);
      }
    },
    handleFileUploadClick() {
      this.$refs.fileInput.click();
    },
    loadUserInfo() {
      console.log('Loading user info...');
      const firstName = localStorage.getItem('firstname') || 'Guest';
      const lastName = localStorage.getItem('lastname') || '';
      const email = localStorage.getItem('email') || '';

      console.log('Email from localStorage:', email);  
      this.userInfo = { firstName, lastName, email };  
    }
  },
  mounted() {
    this.updateDateTime();
    this.timer = setInterval(this.updateDateTime, 1000);
    this.loadUserInfo();
  },
  beforeDestroy() {
    clearInterval(this.timer);
  },
};
</script>

<style scoped>
.header-container {
  position: fixed;
  top: 0;
  right: 0;
  left: 250px;
  width: calc(100% - 250px); 
  padding: 10px 20px;
  z-index: 1000;
  transition: all 0.3s ease;
}

.header-card {
  background-color: #DBF4F8;
  border-radius: 15px;
  width: 100%;
  height: auto;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.card-body {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}

.card-title {
  font-size: 1.5rem;
  color: black;
}

.profile-section {
  display: flex;
  align-items: center;
  font-size: 14px;
}

.user-info {
  font-size: 14px;
  color: black;
}

.material-icons {
  font-size: 24px;
  color: black;
}

.profile-section i.material-icons {
  font-size: 24px;
}

.user-info i.material-icons {
  font-size: 36px;
  color: black;
  margin-right: 8px;
}

.datetime-display {
  opacity: 0.6;
  font-size: 0.8rem;
  color: #333;
  position: absolute;
  bottom: 8px;
  left: 20px;
}

.dropdown-menu-left {
  left: -10px; 
}

@media (max-width: 992px) {
  .header-container {
    left: 80px;
    width: calc(100% - 80px); 
  }
}

@media (max-width: 768px) {
  .card-title {
    font-size: 1.25rem;
  }

  .profile-section {
    font-size: 12px;
  }

  .user-info {
    font-size: 12px;
  }

  .material-icons {
    font-size: 20px;
  }

  .profile-section i.material-icons {
    font-size: 20px;
  }

  .user-info i.material-icons {
    font-size: 28px;
  }
}

@media (max-width: 576px) {
  .header-container {
    left: 0;
    width: 100%;
    padding: 10px;
    z-index: 999; /* Ensure header is below the hamburger menu */
  }

  .card-title {
    display: none;
  }

  .profile-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .user-info {
    font-size: 12px;
    margin-top: 8px;
  }

  .user-info i.material-icons {
    font-size: 30px;
  }

  .profile-section {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    padding-right: 15px;
  }

  .dropdown .material-icons {
    transform: translateY(2px); /* Move down slightly without affecting padding */
    font-size: 30px;
    margin-left: -100px;
  }

  .datetime-display {
    font-size: 0.7rem;
    bottom: 5px;
    left: 15px; 
  }
}

/* Dialog Styles */
.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.dialog-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  width: 80%;
  max-width: 600px;
}

.profile-picture-container {
  margin-bottom: 20px;
}

.profile-picture {
  position: relative;
  display: inline-block;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  background-color: #ddd;
  margin: 0 auto;
}

.profile-picture-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-picture-input {
  position: absolute;
  bottom: 5px;
  right: 5px;
  opacity: 0;
  cursor: pointer;
}

.form-group {
  margin-bottom: 15px;
}

.form-actions {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}

.btn-link {
  padding: 0;
  font-size: 14px;
}

</style>
