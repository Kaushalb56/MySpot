function switchForm(type) {
  const customerForm = document.getElementById("customerForm");
  const restaurantForm = document.getElementById("restaurantForm");
  const customerBtn = document.getElementById("customerBtn");
  const restaurantBtn = document.getElementById("restaurantBtn");

  const activeClasses = ["bg-red-500", "text-white"];
  const inactiveClasses = ["bg-white", "text-gray-700"];

  const toggleForm = (show, hide) => {
    show.classList.remove("hidden");
    hide.classList.add("hidden");
  };

  const toggleButton = (activeBtn, inactiveBtn) => {
    activeBtn.classList.add(...activeClasses);
    activeBtn.classList.remove(...inactiveClasses);
    inactiveBtn.classList.add(...inactiveClasses);
    inactiveBtn.classList.remove(...activeClasses);
  };

  if (type === "customer") {
    toggleForm(customerForm, restaurantForm);
    toggleButton(customerBtn, restaurantBtn);
  } else {
    toggleForm(restaurantForm, customerForm);
    toggleButton(restaurantBtn, customerBtn);
  }
}