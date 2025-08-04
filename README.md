# 🚀 Laravel Projects Collection

Welcome to my Laravel Projects repository!  
This collection includes all the Laravel-based web applications I’ve built for learning, practice, and real-world use.

---

## 📚 About Laravel

**Laravel** is a PHP framework for building modern, fast, and secure web applications.  
It follows the MVC (Model-View-Controller) pattern and comes with built-in tools for routing, database migrations, authentication, and more.

---

## 📁 Projects Included

Each folder in this repository is a separate Laravel project. Examples:

- ✅ `task-manager` – Manage tasks with CRUD functionality

> 🔧 Each project includes its own README and setup instructions.

---

## 🛠️ Tools & Technologies

- Laravel (PHP Framework)
- MySQL / SQLite Database
- Blade Templates / Vue.js
- Composer & Artisan CLI
- Laravel Mix (SCSS & JS build)
- Optional: Tailwind CSS, Livewire, Sanctum, Jetstream

---

## ⚙️ How to Run a Project

> Follow these steps to run any project in this repository:

```bash
# 1. Clone this repository
git clone https://github.com/your-username/laravel-projects.git

# 2. Go into a project folder
cd laravel-projects/project-name

# 3. Install PHP dependencies
composer install

# 4. Install frontend assets (optional)
npm install && npm run dev

# 5. Copy .env and generate app key
cp .env.example .env
php artisan key:generate

# 6. Run migrations (if needed)
php artisan migrate

# 7. Start the development server
php artisan serve
