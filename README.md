# MySpot 🍽️

**MySpot** is a PHP-based restaurant reservation portal that provides two user interfaces:

- 🧑‍💼 **Restaurant Owners**: Manage tables, reservations, and menu items,profile
- 👥 **Customers**: Browse restaurants, view menus, and make reservations

---

## 🚀 Features

- Customer sign-up/login system
- Restaurant admin dashboard
- Menu management (add/edit/delete items)
- Table availability and reservation tracking
- Reservation history
- Reservation confirmation and cancellation email
- Responsive UI 

---

## 🛠️ Tech Stack

- **Backend**: PHP (Core)
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL (via XAMPP)
- **Email Support**: PHPMailer (configured externally)

---

## ⚙️ Setup Instructions

1. Clone the repo or download ZIP
2. Place the folder inside:  
   `C:/xampp/htdocs/`
3. Import the SQL file into **phpMyAdmin**
4. Update your `config.php` file with:
   - DB name
   - Email/app password (for PHPMailer)

---

## 🔒 Security Note

Sensitive email credentials (used for email sending) are not included in this repo.  
Please add your own credentials inside cancel_reservation.php, update_reservation_status.php and final_confirmation.php file.
---

## 📷 Screenshots



---

## 📄 License

MIT License — feel free to use or modify as needed.

---

## ✨ Credits

Developed with ❤️ by kaushal  
