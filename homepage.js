document.addEventListener("DOMContentLoaded", async () => {
  const loginBtn = document.getElementById("openLogin");
  const createBtn = document.getElementById("openCreate");
  const modal = document.getElementById("accountModal");
  const closeModal = document.getElementById("closeModal");
  const form = document.getElementById("modalForm");
  const swapMode = document.getElementById("swapMode");
  const modalTitle = document.getElementById("modalTitle");
  const primaryAction = document.getElementById("primaryAction");
  const msgDiv = document.getElementById("msgRegister");

  const authButtons = document.getElementById("authButtons");
  const userWelcome = document.getElementById("userWelcome");
  const welcomeMsg = document.getElementById("welcomeMsg");
  const logoutBtn = document.getElementById("logoutBtn");
  const deleteAccountBtn = document.getElementById("deleteAccountBtn");

  let mode = "login";
  let currentUser = JSON.parse(localStorage.getItem("currentUser")) || null;
  renderLoggedInState();

  function renderLoggedInState() {
    if (currentUser) {
      authButtons.style.display = "none";
      userWelcome.style.display = "flex";
      welcomeMsg.textContent = `Welcome, ${currentUser.firstName}!`;
    } else {
      authButtons.style.display = "flex";
      userWelcome.style.display = "none";
      welcomeMsg.textContent = "";
    }
  }

  // Modal controls
  loginBtn.onclick = () => openModal("login");
  createBtn.onclick = () => openModal("create");
  closeModal.onclick = () => closeModalFn();

  function openModal(type) {
    mode = type;
    modal.setAttribute("aria-hidden", "false");
    modal.classList.add("is-open");
    renderForm(type);
  }

  function closeModalFn() {
    modal.setAttribute("aria-hidden", "true");
    modal.classList.remove("is-open");
  }

  swapMode.onclick = () => {
    mode = mode === "login" ? "create" : "login";
    renderForm(mode);
  };

  function renderForm(type) {
    const fields = document.getElementById("modalFields");
    modalTitle.textContent = type === "login" ? "Login" : "Create Account";
    primaryAction.textContent = type === "login" ? "Login" : "Register";
    swapMode.textContent = type === "login" ? "Create Account" : "Back to Login";
    msgDiv.textContent = "";

    if (type === "login") {
      fields.innerHTML = `
        <div class="row"><label>Email</label><input id="loginEmail" type="email" required></div>
        <div class="row"><label>Password</label><input id="loginPassword" type="password" required></div>
      `;
    } else {
      fields.innerHTML = `
        <div class="row"><label>First Name</label><input id="firstName" type="text" required></div>
        <div class="row"><label>Last Name</label><input id="lastName" type="text" required></div>
        <div class="row"><label>Email</label><input id="email" type="email" required></div>
        <div class="row"><label>Password</label><input id="password" type="password" required></div>
      `;
    }
  }

  // Submit form
  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    try {
      const data = await getJSONData();
      let users = Array.isArray(data) ? data : [];

      if (mode === "login") {
        const email = document.getElementById("loginEmail").value;
        const password = document.getElementById("loginPassword").value;
        const user = users.find(u => u.email === email && u.password === password);

        msgDiv.style.color = user ? "var(--success)" : "var(--danger)";
        msgDiv.textContent = user ? `Welcome ${user.firstName}!` : "Invalid login.";

        if (user) {
          currentUser = user;
          localStorage.setItem("currentUser", JSON.stringify(currentUser));
          renderLoggedInState();
          setTimeout(closeModalFn, 500);
        }
      } else {
        const newUser = {
          firstName: document.getElementById("firstName").value,
          lastName: document.getElementById("lastName").value,
          email: document.getElementById("email").value,
          password: document.getElementById("password").value,
          favorites: [],
          movies: []
        };

        users.push(newUser);
        const ok = await putJSONData(users);
        msgDiv.style.color = ok ? "var(--success)" : "var(--danger)";
        msgDiv.textContent = ok ? "✅ Account created!" : "❌ Error saving account.";

        if (ok) {
          currentUser = newUser;
          localStorage.setItem("currentUser", JSON.stringify(currentUser));
          renderLoggedInState();
          setTimeout(closeModalFn, 500);
        }
      }
    } catch (err) {
      console.error(err);
      msgDiv.style.color = "var(--danger)";
      msgDiv.textContent = "Error fetching or saving data.";
    }
  });

  // Logout
  logoutBtn?.addEventListener("click", () => {
    currentUser = null;
    localStorage.removeItem("currentUser");
    renderLoggedInState();
  });

  // Delete account
  deleteAccountBtn?.addEventListener("click", async () => {
    if (!currentUser) return;

    const confirmed = confirm("Are you sure you want to delete your account? This cannot be undone.");
    if (!confirmed) return;

    try {
      const data = await getJSONData();
      let users = Array.isArray(data) ? data : [];

      users = users.filter(u => u.email !== currentUser.email);
      const ok = await putJSONData(users);

      if (ok) {
        alert("Your account has been deleted.");
        currentUser = null;
        localStorage.removeItem("currentUser");
        renderLoggedInState();
      } else {
        alert("Failed to delete account.");
      }
    } catch (err) {
      console.error(err);
      alert("Error deleting account: " + err);
    }
  });
});
