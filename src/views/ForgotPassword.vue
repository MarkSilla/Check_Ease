<template>
  <div id="app">
    <!-- Background and Form Container -->
    <div class="background-container">
      <div class="square" v-for="n in 26" :key="n" :class="'square-' + n"></div>
    </div>

    <div class="container d-flex justify-content-center align-items-center min-vh-1000 position-absolute top-50 start-50 translate-middle">
      <div class="box-container w-100" style="max-width: 400px;">
        <!-- Logo Section -->
        <div class="mb-4 text-center">
          <img src="/public/images/checkEaseLogo.png" alt="logo here" class="img-fluid" style="max-width: 100%; height: auto;" />
        </div>

        <!-- Form Section -->
        <form @submit.prevent="submitForm">
          <h2 class="text-center"><b>Reset Your Password</b></h2>
          <p class="text-secondary text-center mb-4">
              Go back to
              <router-link to="/login" class="text-decoration-none">Login page</router-link>
            </p>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" id="email" v-model="email" class="form-control" :class="{'is-invalid': emailError}" @blur="validateEmail" required />
            <div v-if="emailError" class="invalid-feedback">
              {{ emailError }}
            </div>
          </div>

          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" id="newPassword" v-model="newPassword" class="form-control" :class="{'is-invalid': passwordError}" @input="validatePassword" required />
            <div v-if="passwordError" class="invalid-feedback">
              {{ passwordError }}
            </div>
          </div>

          <div class="mb-3">
            <label for="reenterPassword" class="form-label">Re-enter Password</label>
            <input type="password" id="reenterPassword" v-model="reenterPassword" class="form-control" :class="{'is-invalid': passwordMatchError}" @input="validatePasswordMatch" required />
            <div v-if="passwordMatchError" class="invalid-feedback">
              {{ passwordMatchError }}
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn btn-primary w-100"
            :disabled="isFormInvalid"
          >
            Submit
          </button>

          <!-- Copyright -->
          <div class="text-center mt-4">
            <small class="text-muted">&copy;2024 Databoys</small>
          </div>
        </form>
      </div>
    </div>

    <!-- Custom Success Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Password Reset Successful</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p>Your password has been reset successfully. You will now be redirected to the login page.</p>
        </div>
        <div class="modal-footer">
          <button @click="redirectToLogin" class="btn btn-primary">Go to Login</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      email: "",
      newPassword: "",
      reenterPassword: "",
      emailError: "",
      passwordError: "",
      passwordMatchError: "",
      showModal: false,
    };
  },
  computed: {
    isFormInvalid() {
      // Check whether any of the error messages are present
      const hasError =
        this.emailError || this.passwordError || this.passwordMatchError;
      return !!hasError;
    },
  },
  methods: {
    validateEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(this.email)) {
        this.emailError = "Please enter a valid email address.";
      } else {
        this.emailError = "";
      }
    },
    validatePassword() {
      const passwordRegex =
        /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d]{8,}$/;
      if (!passwordRegex.test(this.newPassword)) {
        this.passwordError =
          "Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, and a number.";
      } else {
        this.passwordError = "";
      }
    },
    validatePasswordMatch() {
      if (this.newPassword !== this.reenterPassword) {
        this.passwordMatchError = "Passwords do not match.";
      } else {
        this.passwordMatchError = "";
      }
    },
    async submitForm() {
      this.validateEmail();
      this.validatePassword();
      this.validatePasswordMatch();

      if (this.isFormInvalid) {
        return;
      }

      // Perform the API request
      try {
        const response = await axios.post(
          "http://localhost/CheckEaseNEW-main/vue-login-backend/forgotPassword.php",
          {
            email: this.email,
            newPassword: this.newPassword,
            reenterPassword: this.reenterPassword,
          }
        );

        if (response.data.message === "Password reset successfully.") {
          this.showModal = true;
        } else if (response.data.message === "Email not found.") {
          this.emailError = "Email not found.";
        } else {
          alert(response.data.message || "An error occurred.");
        }
      } catch (error) {
        console.error("Error in form submission:", error);
        alert("An error occurred. Please try again.");
      }
    },
    closeModal() {
      this.showModal = false;
    },
    redirectToLogin() {
      this.$router.push("/login");
    },
  },
};
</script>



  <style scoped>
 .modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.3s ease;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  max-width: 500px;
  width: 100%;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  animation: slideUp 0.3s ease-out;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.modal-header h3 {
  margin: 0;
}

.close-btn {
  font-size: 30px;
  background: none;
  border: none;
  color: #999;
  cursor: pointer;
}

.close-btn:hover {
  color: #333;
}

.modal-body p {
  font-size: 16px;
  color: #555;
}

.modal-footer {
  margin-top: 20px;
}

.modal-footer .btn {
  padding: 8px 20px;
  font-size: 16px;
}

/* Animations */
@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@keyframes slideUp {
  0% {
    transform: translateY(30px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}
  .background-container {
    position: fixed;
    z-index: -1;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  
  .box-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    padding: 2rem;
  }
  
  .btn-primary {
    background-color: #0d6efd;
    border: none;
  }
  
  .btn-primary:hover {
    background-color: #0a58ca;
  }
  
  .form-control:focus {
    box-shadow: none;
    border-color: #0a58ca;
  }
  
  .square {
    position: absolute;
    background-color: rgba(0, 123, 255, 0.1);
    opacity: 0.5;
  }
  
  .square-1 { width: 100px; height: 100px; top: 10%; left: 10%; }
  .square-2 { width: 150px; height: 150px; top: 20%; left: 60%; }
  .square-3 { width: 120px; height: 120px; top: 70%; left: 30%; }
  .square-4 { width: 130px; height: 130px; top: 40%; left: 80%; }
  .square-5 { width: 140px; height: 140px; top: 30%; left: 20%; }
  .square-6 { width: 110px; height: 110px; top: 60%; left: 40%; }
  .square-7 { width: 160px; height: 160px; top: 50%; left: 50%; }
  .square-8 { width: 90px; height: 90px; top: 20%; left: 80%; }
  .square-9 { width: 150px; height: 150px; top: 80%; left: 10%; }
  .square-10 { width: 130px; height: 130px; top: 10%; left: 50%; }
  .square-11 { width: 170px; height: 170px; top: 70%; left: 20%; }
  .square-12 { width: 120px; height: 120px; top: 90%; left: 60%; }
  .square-13 { width: 140px; height: 140px; top: 30%; left: 90%; }
  .square-14 { width: 100px; height: 100px; top: 20%; left: 10%; }
  .square-15 { width: 160px; height: 160px; top: 40%; left: 30%; }
  .square-16 { width: 110px; height: 110px; top: 60%; left: 50%; }
  .square-17 { width: 150px; height: 150px; top: 50%; left: 80%; }
  .square-18 { width: 120px; height: 120px; top: 10%; left: 30%; }
  .square-19 { width: 130px; height: 130px; top: 80%; left: 40%; }
  .square-20 { width: 140px; height: 140px; top: 10%; left: 60%; }
  .square-21 { width: 90px; height: 90px; top: 20%; left: 70%; }
  .square-22 { width: 150px; height: 150px; top: 30%; left: 20%; }
  .square-23 { width: 130px; height: 130px; top: 50%; left: 10%; }
  .square-24 { width: 170px; height: 170px; top: 70%; left: 30%; }
  .square-25 { width: 120px; height: 120px; top: 90%; left: 50%; }
  .square-26 { width: 140px; height: 140px; top: 20%; left: 80%; }
  </style>
  