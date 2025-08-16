# 🛠 HMS Tech - Admin Dashboard

## 📌 Overview
**HMS Tech - Admin Dashboard** is a comprehensive role-based management system that streamlines collaboration between **Clients, Partners, Developers, Team Managers, and Teams**.  
It provides dedicated dashboards for each role, efficient **project tracking**, **attendance management**, and smooth **partner-client relations**.

---

## 🎯 Key Features

### 🔑 Authentication & Roles
- Secure login & authentication system.
- Role-based dashboards for:
  - **Clients**
  - **Partners**
  - **Developers**
  - **Team Managers**
  - **Admins**

---

### 👥 Partner Management
- Add, edit, and manage partners.
- Auto-fetch email based on associated user ID.
- Partner dashboard for managing assigned projects & clients.

---

### 🧑‍💻 Developer Management
- Manage developers with CRUD operations.
- Assign developers to teams.
- Track workload and project involvement.
- Developer-specific dashboard for tasks & attendance.

---

### 👨‍💼 Team Managers
- Assign developers to teams & projects.
- Manage deadlines and deliverables.
- Approve/reject attendance logs.
- Dedicated manager dashboard.

---

### 🏢 Teams Module
- Create and manage teams.
- Assign team leads and members.
- Monitor team performance & productivity.

---

### 📈 Client Management
- Manage client details and associated partners.
- Assign projects to clients.
- Client dashboard to track project status.

---

### ⏱ Attendance Tracking
- Daily attendance tracking for developers.
- Approval system managed by team leads/managers.
- Admin reports on attendance trends.

---

### 📊 Admin Dashboard
- Global overview of:
  - **Clients**
  - **Partners**
  - **Developers**
  - **Teams**
  - **Attendance**
- Detailed reports & analytics using **ApexCharts**.
- Manage all users with CRUD operations.

---

## ⚙️ Tech Stack
- **Backend**: Laravel (PHP)
- **Frontend**: Blade + Bootstrap + JavaScript
- **Database**: MySQL
- **Authentication**: Laravel Breeze / Sanctum (if API-based)
- **Charts & Reports**: ApexCharts + Lucide Icons

---

## 🚀 Installation & Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/hms-tech-admin-dashboard.git
   cd hms-tech-admin-dashboard
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run dev
   ```
3. Setup environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure `.env` for **DB connection**.
5. Run migrations & seeders:
   ```bash
   php artisan migrate --seed
   ```
6. Start the development server:
   ```bash
   php artisan serve
   ```

---

## 📌 Future Enhancements
- API integration for mobile applications.
- Real-time notifications for project updates.
- Advanced reporting with filters & export options.
- Integration with communication tools (Slack, Email, WhatsApp).

---

## 📜 License
This project is licensed under the **MIT License**.
