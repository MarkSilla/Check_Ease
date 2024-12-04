<template>
  <div class="container d-flex justify-content-center align-items-center vh-100" style="margin-left: 150px;">
    <!-- Form Section -->
    <div class="card p-4 m-5 mt-3" style="width: 850px; height: auto; border-radius: 20px; background-color: #fdf6f5;">
      <form @submit.prevent="createClass" class="d-flex flex-column">
        <!-- Class Name Input -->
        <div class="form-group mb-4">
          <label for="className" class="form-label font-weight-bold">Subject</label>
          <input v-model="className" type="text" class="form-control custom-input" id="className" placeholder="Enter Class Name" required>
        </div>

        <!-- Class Section Input -->
        <div class="form-group mb-4">
          <label for="classSection" class="form-label font-weight-bold">Class Section</label>
          <input v-model="classSection" type="text" class="form-control custom-input" id="classSection" placeholder="Enter Class Section" required>
        </div>

        <!-- Student Capacity Input -->
        <div class="form-group mb-4">
          <label for="capacity" class="form-label font-weight-bold">Student Capacity</label>
          <input 
            v-model="capacity" 
            type="number" 
            class="form-control custom-input" 
            id="capacity" 
            placeholder="Enter Capacity" 
            required
          />
          <!-- Display Capacity Error below the input (only once) -->
          <div v-if="capacityError" class="text-danger mt-1" style="font-size: 12px;">
            {{ capacityError }}
          </div>
        </div>

        <!-- Create Button -->
        <div class="d-flex justify-content-end">
          <button type="submit" class="btn custom-btn"><b>Create</b></button>
        </div>
      </form>
    </div>

    <!-- Display Created Classes Below the Form -->
    <div class="card p-4 mt-4" style="width: 850px; border-radius: 20px; background-color: #fdf6f5;">
      <h5 class="font-weight-bold mb-3">Created Classes</h5>
      <div class="list-group">
        <div v-if="classes.length === 0" class="list-group-item">
          <p>No classes created yet.</p>
        </div>
        <div v-else>
          <div v-for="(classItem, index) in classes" :key="index" class="list-group-item d-flex justify-content-between align-items-start mb-3" style="border-radius: 10px;">
            <!-- Display Class Information in Separate Lines -->
            <div>
              <strong class="class-name">{{ classItem.class_name }}</strong><br>
              <small><strong>Student Capacity: </strong>{{ classItem.capacity }}</small><br>
              <small><strong>Class Section: </strong>{{ classItem.section }}</small><br>
              <small><strong>Class Code: </strong>{{ classItem.class_code || 'N/A'}}</small>
            </div>
            <div>
              <button @click="editClass(classItem)" class="btn btn-primary btn-sm mx-2"><b>Edit</b></button>
              <button @click="deleteClass(classItem)" class="btn btn-danger btn-sm"><b>Delete</b></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ElNotification } from 'element-plus';

const api = axios.create({
  baseURL: 'http://localhost/CheckEaseNEW-main/vue-login-backend',
  headers: {
    'Content-Type': 'application/json',
  },
});

export default {
  data() {
    return {
      className: '',
      classSection: '',  
      class_code: '', 
      capacity: '',
      classes: [],
      capacityError: '',
      token: localStorage.getItem('token') || '',
      isLoading: false,
    };
  },
  methods: {
    showNotification(type, message) {
      ElNotification({
        type,
        message,
        duration: 3000,
      });
    },

    async fetchClasses() {
      if (!this.token) {
        this.showNotification('error', 'You must be logged in to view classes');
        return;
      }

      try {
        const response = await api.get('/fetchClasses.php', {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        });

        if (response.data.success) { 
          this.classes = response.data.classes;
          localStorage.setItem('classes', JSON.stringify(this.classes));  
          console.log('Classes fetched from server:', this.classes);
        } else {
          this.showNotification('error', response.data.error || 'Failed to fetch classes');
          if (response.data.error === 'Invalid or expired token') {
            this.logout();
          }
        }
      } catch (error) {
        console.error('Error fetching classes:', error);
        this.showNotification('error', 'An error occurred while fetching classes');
      }
    },

    async createClass() {
  this.capacityError = '';

  if (!this.className || !this.capacity || !this.classSection) {
    this.capacityError = 'Class name, class section, and capacity are required';
    return;
  }

  if (this.capacity <= 0 || isNaN(this.capacity)) {
    this.capacityError = 'Capacity must be a positive number';
    return;
  }

  if (!this.token) {
    this.showNotification('error', 'You must be logged in to create a class');
    return;
  }

  try {
    this.isLoading = true;
    const response = await api.post(
      '/createclass.php',
      {
        class_name: this.className,
        section: this.classSection,  
        capacity: this.capacity,
      },
      {
        headers: {
          Authorization: `Bearer ${this.token}`,
        },
      }
    );

    if (response.data.success) {
      const newClass = {
        class_name: this.className,
        section: this.classSection,  
        capacity: this.capacity,
        class_code: response.data.class_code, 
      };

      this.classes.unshift(newClass); 
      localStorage.setItem('classes', JSON.stringify(this.classes));  
      this.showNotification('success', 'Class created successfully!');

      this.className = '';
      this.classSection = '';  
      this.capacity = '';
    } else {
      this.showNotification('error', response.data.error || 'Failed to create class');
    }
  } catch (error) {
    console.error('Error creating class:', error);
    this.showNotification('error', 'An error occurred while creating the class');
  } finally {
    this.isLoading = false;
  }
},

    async deleteClass(classItem) {
      console.log('Deleting class:', classItem);
      if (!this.token) {
        this.showNotification('error', 'You must be logged in to delete classes');
        return;
      }

      const classCode = classItem.class_code || classItem.id;

      try {
        const response = await api.delete(`/delete.php?class_code=${classCode}`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        });

        if (response.data.success) {
          this.classes = this.classes.filter((classItem) => classItem.class_code !== classCode);
          localStorage.setItem('classes', JSON.stringify(this.classes));
          this.showNotification('success', 'Class deleted successfully');
        } else {
          this.showNotification('error', response.data.error || 'Failed to delete class');
        }
      } catch (error) {
        console.error('Error deleting class:', error);
        this.showNotification('error', 'An error occurred while deleting the class');
      }
    },

    editClass(classItem) {
      console.log('Editing class:', classItem);
    },

    logout() {
      localStorage.removeItem('token');
      this.token = '';
      this.showNotification('info', 'You have been logged out');
    },
  },

  mounted() {
    this.fetchClasses();
  },
};
</script>



<style scoped>
.class-name {
  font-size: 1.5rem; 
  font-weight: bold;
}
.card {
  border-radius: 20px; 
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); 
  background-color: #fdf6f5; 
}

.font-weight-bold {
  font-weight: bold;
}

.custom-input {
  border-radius: 10px;
  box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.1); 
  padding: 10px;
}

.custom-btn {
  background-color: #66a3c7; 
  color: white;
  border-radius: 10px; 
  padding: 8px 16px;
  border: none;
  font-weight: bold;
  text-transform: uppercase;
}

.custom-btn:hover {
  background-color: #5a8fa3; 
}

.list-group-item {
  background-color: #fdf6f5;
  border: none;
}

.list-group-item:hover {
  background-color: #f2e6e2;
}

.text-danger {
  color: red;
}
</style>
