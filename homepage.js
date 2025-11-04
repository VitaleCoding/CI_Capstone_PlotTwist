// homepage.js
document.addEventListener("DOMContentLoaded", () => {
  const loginBtn = document.getElementById("openLogin");
  const createBtn = document.getElementById("openCreate");
  const modal = document.getElementById("accountModal");
  const closeModal = document.getElementById("closeModal");
  const form = document.getElementById("modalForm");
  const swapMode = document.getElementById("swapMode");
  const modalTitle = document.getElementById("modalTitle");
  const primaryAction = document.getElementById("primaryAction");
  let mode = "login";

  // Open modal
  loginBtn.onclick = () => openModal("login");
  createBtn.onclick = () => openModal("create");
  closeModal.onclick = () => closeModalFn();

  function openModal(type) {
    mode = type;
    modal.setAttribute("aria-hidden", "false");
    modal.style.display = "flex";
    renderForm(type);
  }

  function closeModalFn() {
    modal.setAttribute("aria-hidden", "true");
    modal.style.display = "none";
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

    if (type === "login") {
      fields.innerHTML = `
        <label>Email</label>
        <input id="loginEmail" type="email" required>
        <label>Password</label>
        <input id="loginPassword" type="password" required>
      `;
    } else {
      fields.innerHTML = `
        <label>First Name</label>
        <input id="firstName" type="text" required>
        <label>Last Name</label>
        <input id="lastName" type="text" required>
        <label>Email</label>
        <input id="email" type="email" required>
        <label>Password</label>
        <input id="password" type="password" required>
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

        alert(user ? `Welcome ${user.firstName}!` : "Invalid login.");
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
        alert(ok ? "✅ Account created!" : "❌ Error saving account.");
      }
    } catch (err) {
      console.error(err);
      alert("Error fetching or saving data.");
    }
  });
});
