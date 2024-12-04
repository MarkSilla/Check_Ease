<template>
  <div class="container position-flex offset-md-2" style="margin-top: 20px">
    <!-- Check if classes are loaded -->
    <div v-if="classes.length > 0" class="row">
      <div
        class="col-12 col-sm-6 col-md-5 col-lg-4 mb-2"
        v-for="(classItem, index) in classes"
        :key="index"
      >
        <div
          class="card custom-card shadow-sm d-flex flex-column align-items-center justify-content-center"
          style="width: 340px; height: 200px;"
        >
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="card-content text-center mb-3">
              <!-- Custom Display Format -->
              <strong class="classSection">{{ classItem.section }}</strong><br>
              <p class="card-text"><strong>Class Name: </strong>{{ classItem.class_name }}</p>
              <p class="card-text"><strong>Class Code: </strong>{{ classItem.class_code }}</p>
            </div>
            <!-- View Attendance and Take Attendance Buttons -->
            <router-link
              :to="{ path: '/ViewStudentAttendance', query: { classCode: classItem.class_code } }"
              class="btn custom-btn"
            >
              <u>View Attendance</u>
            </router-link>
            <router-link
              :to="{ path: '/attendancerecord', query: { classCode: classItem.class_code } }"
              class="btn custom-btn"
            >
              <u>Take Attendance</u>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- If no classes are found in localStorage -->
    <div v-else class="text-center">
      <p class="lead">It looks like you haven't created any classes yet.</p>
      <p>Please visit the <router-link to="/class" class="text-primary"><strong>Create Class</strong></router-link> page to add your first class.</p>
    </div>
  </div>
</template>


<script>
export default {
  name: "AttendanceRecord",
  data() {
    return {
      classes: [], 
    };
  },
  methods: {
    loadClassesFromLocalStorage() {
      const storedClasses = localStorage.getItem('classes');
      if (storedClasses) {
        this.classes = JSON.parse(storedClasses);
        console.log('Loaded classes from localStorage:', this.classes);
      } else {
        console.log("No classes found.");
      }
    },

    handleResize() {
      const width = window.innerWidth;
      const container = document.querySelector('.container');
      const cards = document.querySelectorAll('.custom-card');
  
      if (width <= 576) { 
        container.style.padding = '10px';
        cards.forEach(card => {
          card.style.width = '85%';
          card.style.height = '150px';
          card.style.margin = '10px auto';
        });
      } else if (width <= 768) { 
        container.style.padding = '15px';
        cards.forEach(card => {
          card.style.width = '90%';
          card.style.height = '180px';
          card.style.margin = '12px auto';
        });
      } else { 
        container.style.padding = '20px';
        cards.forEach(card => {
          card.style.width = '340px';
          card.style.height = '200px';
          card.style.margin = '15px';
        });
      }
    }
  },
  watch: {
    '$route'(to, from) {
      this.loadClassesFromLocalStorage();
    }
  },
  mounted() {
    this.loadClassesFromLocalStorage(); 
    this.handleResize();
    window.addEventListener('resize', this.handleResize);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.handleResize);
  }
};
</script>




<style scoped>
.classSection {
  font-size: 2.5rem; /* Adjust this value as needed */
  font-weight: bold;
}
  .custom-btn {
    margin-top: 5px;
  }
  /* Container styles */
  .container {
    padding: 20px;
    width: 100%;
  }
  
  /* Card grid layout */
  .row {
    display: flex;
    flex-wrap: wrap;
    margin: -10px; /* Negative margin to offset card padding */
  }
  
  /* Card column */
  .col-12 {
    padding: 10px;
  }
  
  /* Card styles */
  .custom-card {
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #96D4DE;
  }
  
  .card {
    border-radius: 15px;
    margin-left: 45px;
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    background-color: #96D4DE;
    height: 200px;
    position: relative;
  }
  
  .card:hover {
    transition: all 0.2s ease-out;
    box-shadow: 0px 4px 8px rgba(38, 38, 38, 0.2);
    top: -4px;
    border: 1px solid #00838d;
    background-color: #00838d;
  }
  
  .card:hover .custom-btn {
    background-color: #96D4DE;
    color: black;
  }
  
  .card-title {
    font-size: 24px;
  }
  
  .card:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: -16px;
    right: -16px;
    background: #00838d;
    height: 32px;
    width: 32px;
    border-radius: 32px;
    transform: scale(2);
    transform-origin: 50% 50%;
    transition: transform 0.15s ease-out;
  }
  
  .card:hover:before {
    transform: scale(2.15);
  }
  
  .custom-btn {
    width: 150%;
    max-width: 200px;
    background-color: #00838d;
    color: #fff;
    border: none;
    border-radius: 15px;
    text-align: center;
    padding: 8px 0;
    font-size: 16px;
    transition: background-color 0.2s ease-in-out;
  }
  
  @media (max-width: 576px) {
    .container {
      margin-top: 100px;
      margin-bottom: 50px;
    }
    .card {
      margin-left: 30px;
    }
  }
  
  .container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 70px);
  }
  
  .row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 1200px;
  }
  
  .custom-card {
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #96D4DE;
    border-radius: 15px;
  }
  
  .card-title {
    transition: font-size 0.3s ease;
  }
  
  @media (max-width: 576px) {
    .card-title {
      font-size: 18px;
    }
    
    .custom-btn {
      font-size: 14px;
      padding: 6px 12px;
    }
  }
  
  @media (max-width: 360px) {
    .custom-card {
      width: 90%;
      height: 140px;
    }
    
    .card-title {
      font-size: 16px;
    }
  }
  
  /* Prevent content overflow */
  .card-content {
    width: 100%;
    padding: 15px;
    text-align: center;
  }
  
  /* Smooth transitions */
  * {
    transition: all 0.3s ease-in-out;
  }
  
  .container {
    margin-top: 100px;
  }
  
  .row {
    display: flex;
    flex-wrap: wrap;
    margin: -10px;
  }
  
  /* Card column default styles */
  .col-12 {
    padding: 10px;
  }
  
  /* Desktop view (default) */
  @media (min-width: 769px) {
    .col-12 {
      width: calc(33.333% - 20px);
      margin: 10px;
    }
  
    .custom-card {
      width: 340px;
      height: 200px;
      margin-left: 45px;
    }
    
    .container {
      margin-left: 250px; /* Sidebar offset */
    }
  }
  
  /* Tablet view */
  @media (min-width: 577px) and (max-width: 768px) {
    .col-12 {
      width: calc(50% - 20px);
      margin: 10px;
    }
  
    .custom-card {
      width: 90%;
      height: 180px;
      margin: 12px auto;
    }
  }
  
  /* Mobile view */
  @media (max-width: 576px) {
    .container {
      margin-top: 70px;
      padding: 10px;
    }
  
    .col-12 {
      width: 100%;
    }
  
    .custom-card {
      width: 85%;
      height: 150px;
      margin: 10px auto;
    }
  
    .card-title {
      font-size: 18px;
    }
  
    .custom-btn {
      font-size: 14px;
      padding: 6px 12px;
    }
  }
  
  /* Extra small devices */
  @media (max-width: 360px) {
    .custom-card {
      width: 90%;
      height: 140px;
    }
  }
  
  /* Shared card styles */
  .custom-card {
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #96D4DE;
    transition: all 0.2s ease-out;
  }
  </style>